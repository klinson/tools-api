<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_user_id')->default(0);
            $table->unsignedInteger('to_user_id')->default(0);
            $table->unsignedInteger('is_favour')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favours');
    }
}
