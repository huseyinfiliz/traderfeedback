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

class ListFeedbacksController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $params = $request->getQueryParams();

        $userId = Arr::get($params, 'filter.user');
        $type = Arr::get($params, 'filter.type');
        $sort = Arr::get($params, 'filter.sort', 'newest');
        $limit = min((int) Arr::get($params, 'page.limit', 20), 50);
        if ($limit <= 0) {
            $limit = 20;
        }
        $offset = max((int) Arr::get($params, 'page.offset', 0), 0);

        $query = Feedback::query()->with(['fromUser', 'toUser', 'discussion']);

        if ($userId) {
            $query->where('to_user_id', (int) $userId);
        }

        // Onay bekleyen geri bildirimler ana listede (admin/moderatör dahil) HİÇ listelenmez; yalnızca onaylanmışlar listelenir.
        $query->where('is_approved', true);

        if ($type && in_array($type, [Feedback::TYPE_POSITIVE, Feedback::TYPE_NEUTRAL, Feedback::TYPE_NEGATIVE], true)) {
            $query->where('type', $type);
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $feedbacks = $query->skip($offset)->take($limit)->get();

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
            'data'     => $data,
            'included' => array_values($includedMap),
        ]);
    }
}
