<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('attach_id');
    $table->string('name', 255);
    $table->string('path', 255);
    $table->integer('user_id');
    $table->string('type', 100);
    $table->integer('size');
    $table->string('md5', 100);
    $table->timestamps();
    $table->softDeletes();

    $table->index('path');
    $table->index('user_id');
    $table->index('md5');
};
