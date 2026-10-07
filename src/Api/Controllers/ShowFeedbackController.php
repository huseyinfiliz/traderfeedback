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

class ShowFeedbackController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $routeParams = $request->getAttribute('routeParameters') ?? [];
        $id = $routeParams['id'] ?? Arr::get($request->getQueryParams(), 'id');

        $feedback = Feedback::with(['fromUser', 'toUser', 'discussion'])->findOrFail($id);

        $actor->assertCan('view', $feedback);

        $included = [];
        if ($feedback->fromUser) {
            $included[] = FeedbackSerializer::user($feedback->fromUser);
        }
        if ($feedback->toUser) {
            $included[] = FeedbackSerializer::user($feedback->toUser);
        }
        if ($feedback->discussion) {
            $included[] = FeedbackSerializer::discussion($feedback->discussion);
        }

        return new JsonResponse([
            'data' => FeedbackSerializer::feedback($feedback, $actor),
            'included' => $included,
        ]);
    }
}
