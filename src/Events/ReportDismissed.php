<?php

namespace HuseyinFiliz\TraderFeedback\Events;

use Flarum\User\User;
use HuseyinFiliz\TraderFeedback\Models\FeedbackReport;

class ReportDismissed
{
    public function __construct(
        public FeedbackReport $report,
        public User $actor
    ) {
    }
}
