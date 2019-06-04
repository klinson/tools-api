<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wxapp_openid');
            $table->string('nickname');
            $table->string('name', 50);
            $table->tinyInteger('sex')->default(0);
            $table->string('avatar')->nullbale();
            $table->json('wechat_info')->nullbale();

            $table->string('mobile', 20)->nullable()->comment('手机');
            $table->tinyInteger('has_enabled')->default(1)->comment('是否启用');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
