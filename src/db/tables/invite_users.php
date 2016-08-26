<?php

<?php

use Illuminate\Database\Schema\Blueprint;

return function (Blueprint $table): void
{
    $table->increments('invite_users_id');
    $table->integer('users_id');
    $table->integer('invite_code');
    $table->timestamps();
    $table->softDeletes();

    $table->index('users_id');
    $table->index('invite_code');
};