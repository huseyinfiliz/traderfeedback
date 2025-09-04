<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        $schema->create('tfb_feedbacks', function (Blueprint $table) {
            $table->increments('id');

            // User relationships
            $table->unsignedInteger('from_user_id');
            $table->unsignedInteger('to_user_id');

            // Feedback details
            $table->string('type'); // positive, negative, neutral
            $table->string('role'); // buyer, seller, trader
            $table->text('comment');
            $table->unsignedInteger('discussion_id')->nullable(); // Changed to discussion_id

            // Moderation
            $table->boolean('is_approved')->default(true);

            // Timestamps
            $table->timestamps();

            // Foreign keys
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            // Removed foreign key for approved_by_id

            // Indexes
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('type');
            $table->index('created_at');
        });
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('tfb_feedbacks');
    },
];