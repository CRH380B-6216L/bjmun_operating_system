<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('school_id')->unsigned()->nullable();
            $table->enum('type', ['ot', 'dais', 'delegate', 'observer', 'volunteer']);
            $table->enum('gender', ['male', 'female']);
            $table->string('password');
            $table->boolean('enabled');
            $table->timestamps();
            $table->unique(['user_id', 'conference_id', 'type']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regs');
    }
}
