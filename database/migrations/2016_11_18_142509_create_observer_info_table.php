<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObserverInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observer_info', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('school_id')->unsigned();
            $table->enum('status', ['reg','sVerified', 'oVerified', 'paid'])->default('reg');
            $table->enum('gender', ['male','female']);
            $table->string('sfz');
            $table->integer('grade');
            $table->string('email');
            $table->string('qq');
            $table->string('wechat');
            $table->string('parenttel');
            $table->string('tel');
            $table->boolean('accomodate');
            $table->string('roommatename');
            $table->integer('roommate_user_id')->nullable()->unsigned();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('roommate_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('observer_info');
    }
}
