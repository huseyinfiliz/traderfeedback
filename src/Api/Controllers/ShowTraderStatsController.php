<?php

namespace HuseyinFiliz\TraderFeedback\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use HuseyinFiliz\TraderFeedback\Api\Serializers\TraderStatsSerializer;
use HuseyinFiliz\TraderFeedback\Models\TraderStats;
use HuseyinFiliz\TraderFeedback\Models\Feedback;
use Carbon\Carbon;

class ShowTraderStatsController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = TraderStatsSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($request->getQueryParams(), 'id');
        
        // Stats'ı yeniden hesapla - sadece onaylı feedbackleri say
        $stats = TraderStats::firstOrNew(['user_id' => $userId]);
        
        // Sadece onaylanmış feedbackleri say
        $stats->positive_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'positive')
            ->where('is_approved', true)
            ->count();
            
        $stats->neutral_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'neutral')
            ->where('is_approved', true)
            ->count();
            
        $stats->negative_count = Feedback::where('to_user_id', $userId)
            ->where('type', 'negative')
            ->where('is_approved', true)
            ->count();
        
        // Score'u hesapla (pozitif feedback yüzdesi)
        $total = $stats->positive_count + $stats->neutral_count + $stats->negative_count;
        $stats->score = $total > 0 ? ($stats->positive_count / $total) * 100 : 0;
        $stats->last_updated = Carbon::now();
        
        // Eğer yeni kayıtsa kaydet, değilse güncelle
        if (!$stats->exists) {
            $stats->save();
        } else {
            $stats->update();
        }
        
        return $stats;
    }
}