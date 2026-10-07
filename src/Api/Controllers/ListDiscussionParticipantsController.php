<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Discussion\Discussion;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\TraderFeedback\Api\Serializer\FeedbackSerializer;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListDiscussionParticipantsController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $discussionId = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        if (!$discussionId) {
            $path = $request->getUri()->getPath();
            if (preg_match('/\/trader\/discussions\/(\d+)\/participants/', $path, $matches)) {
                $discussionId = $matches[1];
            }
        }

        if (!$discussionId) {
            throw new ValidationException([
                'id' => 'Discussion ID is required.',
            ]);
        }

        $discussion = Discussion::findOrFail($discussionId);

        $actor->assertCan('view', $discussion);

        $search = Arr::get($request->getQueryParams(), 'filter.q');

        $query = $discussion->participants()
            ->where('users.id', '!=', $actor->id);

        if ($search) {
            $query->where('users.username', 'like', '%'.$search.'%');
        }

        $participants = $query->limit(100)->get();

        $data = $participants->map(fn ($user) => FeedbackSerializer::user($user))->all();

        return new JsonResponse([
            'data' => $data,
        ]);
    }
}
