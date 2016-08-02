<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('uid');
    $table->string('phone');
    $table->string('username', 100);
    $table->bigInteger('avatar')->default(0);
    $table->smallInteger('age')->default(0);
    $table->smallInteger('height')->defalut(170);
    $table->smallInteger('kg')->defalut(60);
    $table->enum('role', ['1', '0.5', '0'])->defalut('0.5');
    $table->primary('id');
    $table->unique('phone');
    $table->unique('name');
};