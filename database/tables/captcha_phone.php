<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('captcha_phone_id');
    $table->string('phone', 100);
    $table->string('captcha_code', 50);
    $table->integer('expires')->nullable()->default(3600);
    $table->timestamps();
    $table->softDeletes();

    $table->index('phone');
};
