<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('tfb_stats')) {
            $schema->create('tfb_stats', function (Blueprint $table) {
                $table->increments('id');

                // User relationship
                $table->unsignedInteger('user_id')->unique();

                // Statistics
                $table->unsignedInteger('positive_count')->default(0);
                $table->unsignedInteger('neutral_count')->default(0);
                $table->unsignedInteger('negative_count')->default(0);
                $table->decimal('score', 5, 2)->default(0); // Percentage score (0-100)

                // Timestamp for last update
                $table->timestamp('last_updated')->nullable();

                // Foreign key
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Index
                $table->index('user_id');
                $table->index('score');
            });
        }
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('tfb_stats');
    },
];