<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        $schema->create('tfb_reports', function (Blueprint $table) {
            $table->increments('id');

            // Relationships
            $table->unsignedInteger('user_id'); // User reporting the feedback
            $table->unsignedInteger('feedback_id'); // The reported feedback

            // Report details
            $table->text('reason');
            $table->boolean('resolved')->default(false);
            $table->unsignedInteger('resolved_by_id')->nullable(); // Admin who resolved the report

            // Timestamps
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('tfb_feedbacks')->onDelete('cascade');
            $table->foreign('resolved_by_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index('feedback_id');
            $table->index('resolved');
        });
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('tfb_reports');
    },
];