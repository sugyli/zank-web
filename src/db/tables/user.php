<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('uid');
    $table->string('phone');
    $table->string('username', 100);
    $table->bigInteger('avatar')->nullable()->default(0);
    $table->smallInteger('age')->nullable()->default(0);
    $table->smallInteger('height')->nullable()->default(170);
    $table->smallInteger('kg')->nullable()->default(60);
    $table->enum('role', ['1', '0.5', '0'])->nullable()->default('0.5');
    $table->string('geohash', 100);
    $table->string('latitude', 100);
    $table->string('longitude', 100);
    $table->timestamps();
    $table->softDeletes();
    $table->unique('phone');
    $table->unique('username');
    $table->index('geohash', 'idx_geohash');
    $table->index('latitude', 'idx_geohash');
    $table->index('longitude', 'idx_geohash');
    $table->index('age');
    $table->index('height');
    $table->index('kg');
    $table->index('role');
};