<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListPendingFeedbacksController implements RequestHandlerInterface
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

        $feedbacks = Feedback::where('is_approved', false)
            ->with(['fromUser', 'toUser', 'discussion'])
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $data = [];
        $includedMap = [];

        foreach ($feedbacks as $fb) {
            $data[] = FeedbackSerializer::feedback($fb, $actor);

            if ($fb->fromUser && !isset($includedMap['users:'.$fb->fromUser->id])) {
                $includedMap['users:'.$fb->fromUser->id] = FeedbackSerializer::user($fb->fromUser);
            }
            if ($fb->toUser && !isset($includedMap['users:'.$fb->toUser->id])) {
                $includedMap['users:'.$fb->toUser->id] = FeedbackSerializer::user($fb->toUser);
            }
            if ($fb->discussion && !isset($includedMap['discussions:'.$fb->discussion->id])) {
                $includedMap['discussions:'.$fb->discussion->id] = FeedbackSerializer::discussion($fb->discussion);
            }
        }

        return new JsonResponse([
            'data' => $data,
            'included' => array_values($includedMap),
        ]);
    }
}
