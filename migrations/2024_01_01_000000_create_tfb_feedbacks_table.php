<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('tfb_feedbacks')) {
            $schema->create('tfb_feedbacks', function (Blueprint $table) {
                $table->increments('id');

                // User relationships
                $table->unsignedInteger('from_user_id');
                $table->unsignedInteger('to_user_id');

                // Feedback details
                $table->string('type'); // positive, negative, neutral
                $table->string('role'); // buyer, seller, trader
                $table->text('comment');
                $table->unsignedInteger('discussion_id')->nullable();

                // Moderation
                $table->boolean('is_approved')->default(true);
                $table->unsignedInteger('approved_by_id')->nullable();

                // Timestamps
                $table->timestamps();

                // Foreign keys
                $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('approved_by_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('set null');

                // Indexes
                $table->index('from_user_id');
                $table->index('to_user_id');
                $table->index('type');
                $table->index('is_approved');
                $table->index('created_at');
                
                // Unique constraint: bir kullanıcı bir kullanıcıya bir discussion için tek feedback verebilir
                $table->unique(['from_user_id', 'to_user_id', 'discussion_id'], 'unique_feedback_per_discussion');
            });
        }
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('tfb_feedbacks');
    },
];