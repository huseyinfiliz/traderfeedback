<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListReportsController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('huseyinfiliz-traderfeedback.moderate');

        $params = $request->getQueryParams();
        $limit = min((int) Arr::get($params, 'page.limit', 50), 100);
        if ($limit <= 0) {
            $limit = 50;
        }
        $offset = max((int) Arr::get($params, 'page.offset', 0), 0);

        $query = FeedbackReport::where('resolved', false)
            ->with(['reporter', 'feedback.fromUser', 'feedback.toUser']);

        $userId = Arr::get($params, 'filter.user');
        if ($userId) {
            $query->whereHas('feedback', function ($q) use ($userId) {
                $q->where('to_user_id', (int) $userId);
            });
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $data = [];
        $includedMap = [];

        foreach ($reports as $report) {
            $data[] = FeedbackSerializer::report($report, $actor);

            if ($report->reporter && !isset($includedMap['users:'.$report->reporter->id])) {
                $includedMap['users:'.$report->reporter->id] = FeedbackSerializer::user($report->reporter);
            }

            if ($report->feedback) {
                if (!isset($includedMap['trader-feedbacks:'.$report->feedback->id])) {
                    $includedMap['trader-feedbacks:'.$report->feedback->id] = FeedbackSerializer::feedback($report->feedback, $actor);
                }

                if ($report->feedback->fromUser && !isset($includedMap['users:'.$report->feedback->fromUser->id])) {
                    $includedMap['users:'.$report->feedback->fromUser->id] = FeedbackSerializer::user($report->feedback->fromUser);
                }

                if ($report->feedback->toUser && !isset($includedMap['users:'.$report->feedback->toUser->id])) {
                    $includedMap['users:'.$report->feedback->toUser->id] = FeedbackSerializer::user($report->feedback->toUser);
                }
            }
        }

        return new JsonResponse([
            'data'     => $data,
            'included' => array_values($includedMap),
        ]);
    }
}
