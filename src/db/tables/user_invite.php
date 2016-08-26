<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table): void
{
    $table->increments('user_invite_id');
    $table->string('invite_code', 255);
    $table->integer('create_users_id');
    $table->integer('use_users_id')->nullable()->default(null);
    $table->timestamps();
    $table->softDeletes();

    $table->index('invite_code');
    $table->index('create_users_id');
    $table->index('use_users_id');
};