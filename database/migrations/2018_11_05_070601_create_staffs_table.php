<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('staffs')) {
            Schema::create('staffs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->unique()->nullable();
                $table->integer('idcard_id')->unsigned()->nullable();
                $table->integer('nationality_id')->unsigned()->nullable();
                $table->integer('role_id')->unsigned()->nullable();
                $table->string('ice')->nullable();
                $table->string('residence')->nullable();
                $table->string('profileurl')->nullable()->default('defaultprofile.jpg');
                $table->integer('language_id')->unsigned()->nullable();
                $table->integer('station_id')->unsigned()->nullable();
                $table->integer('group_id')->unsigned()->nullable();
                $table->integer('status_id')->unsigned()->default(1);
                $table->integer('company_id')->unsigned()->nullable();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('cascade');
                $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staffs');
    }
}
