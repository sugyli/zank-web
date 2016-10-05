<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('area_id')->unsigned();
    $table->string('name', 250);
    $table->integer('order')->nullable()->default(10000);
    $table->integer('parent_area_id')->nullable()->default(0);

    $table->timestamps();
    $table->softDeletes();

    $table->index('parent_area_id');
    $table->index('order');
};
