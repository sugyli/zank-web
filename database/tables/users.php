<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('user_id');
    $table->string('phone'); // 手机号
    $table->string('username', 100); // 用户名
    $table->string('password', 255); // 密码
    $table->string('hash', 100); // 盐值
    $table->bigInteger('avatar')->nullable()->default(0); // 头像附件id
    $table->smallInteger('age')->nullable()->default(0); // 年龄
    $table->smallInteger('height')->nullable()->default(170); // 身高
    $table->smallInteger('kg')->nullable()->default(60); // 体重
    $table->integer('areas_id')->nullable()->default(0); // 地区id
    $table->enum('role', ['1', '0.5', '0', '-1'])->nullable()->default('0.5'); // 角色
    $table->enum('shape', ['壮熊', '狒狒', '肌肉', '普通', '偏瘦'])->nullable()->default('普通'); // 体型
    $table->smallInteger('love')->nullable()->default(0); // 情感状态
    $table->string('geohash', 100)->nullable()->default(null);
    $table->string('latitude', 100)->nullable()->default(null);
    $table->string('longitude', 100)->nullable()->default(null);
    $table->timestamps();
    $table->softDeletes();

    $table->unique('phone');
    $table->unique('username');
    $table->index('geohash');
    $table->index('latitude');
    $table->index('longitude');
    $table->index('age');
    $table->index('height');
    $table->index('kg');
    $table->index('role');
};
