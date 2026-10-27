<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->table('tfb_feedbacks', function (Blueprint $table) {
            $table->index(['from_user_id', 'created_at'], 'tfb_feedback_rate_limit');
        });
        $schema->table('tfb_reports', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'tfb_report_rate_limit');
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('tfb_feedbacks', function (Blueprint $table) {
            $table->dropIndex('tfb_feedback_rate_limit');
        });
        $schema->table('tfb_reports', function (Blueprint $table) {
            $table->dropIndex('tfb_report_rate_limit');
        });
    },
];
