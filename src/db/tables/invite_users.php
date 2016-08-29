<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table)
{
    $table->increments('invite_user_id');
    $table->integer('user_id');
    $table->integer('invite_code');
    $table->timestamps();
    $table->softDeletes();

    $table->index('user_id');
    $table->index('invite_code');
};