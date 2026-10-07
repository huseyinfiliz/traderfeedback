<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use HuseyinFiliz\TraderFeedback\Api\Serializers\MinimalUserSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListDiscussionParticipantsController extends AbstractListController
{
    public $serializer = MinimalUserSerializer::class;

    public $limit = 100;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        
        $discussionId = Arr::get($request->getQueryParams(), 'id');
        
        if (!$discussionId) {
            $path = $request->getUri()->getPath();
            if (preg_match('/\/trader\/discussions\/(\d+)\/participants/', $path, $matches)) {
                $discussionId = $matches[1];
            }
        }

        if (!$discussionId) {
            throw new \Flarum\Foundation\ValidationException([
                'id' => 'Discussion ID is required.'
            ]);
        }

        $discussion = Discussion::query()
            ->where('id', $discussionId)
            ->firstOrFail();

        $actor->assertCan('view', $discussion);

        $search = Arr::get($request->getQueryParams(), 'filter.q');

        $query = $discussion->participants()
            ->where('users.id', '!=', $actor->id);

        if ($search) {
            $query->where('users.username', 'like', '%' . $search . '%');
        }

        $participants = $query
            ->limit($this->limit)
            ->get();

        return $participants;
    }
}
