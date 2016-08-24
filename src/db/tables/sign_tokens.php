<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table)
{
    $table->increments('sign_tokens_id');
    $table->string('token', 255);
    $table->string('refresh_token', 255);
    $table->integer('users_id');
    $table->integer('expires')->nullable()->default(0);
    $table->timestamps();
    $table->softDeletes();
    $table->index('token');
};
