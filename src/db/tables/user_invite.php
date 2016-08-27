<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table): void
{
    $table->increments('user_invite_id');
    $table->string('invite_code', 255);
    $table->integer('create_user_id');
    $table->integer('use_user_id')->nullable()->default(null);
    $table->timestamps();
    $table->softDeletes();

    $table->index('invite_code');
    $table->index('create_user_id');
    $table->index('use_user_id');
};