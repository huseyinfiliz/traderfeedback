<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        $schema->table('tfb_feedbacks', function (Blueprint $table) {
            $table->unsignedInteger('approved_by_id')->nullable()->after('is_approved');
            $table->foreign('approved_by_id')->references('id')->on('users')->onDelete('set null');
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('tfb_feedbacks', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn('approved_by_id');
        });
    },
];