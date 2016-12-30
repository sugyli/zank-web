<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table) {
    $table->increments('bid');
    $table->integer('articleid')->nullable()->default(0);
    $table->tinyInteger('is_use')->nullable()->default(0);//是否以删除  0否 1是
    $table->timestamps();
    $table->softDeletes();

    $table->unique('articleid');
    $table->index(['articleid','is_use']);

};
