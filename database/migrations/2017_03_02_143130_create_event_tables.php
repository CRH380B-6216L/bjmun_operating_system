<?php
/**
* Copyright (C) MUNPANEL
* This file is part of MUNPANEL System.
*
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*
* Developed by Adam Yi <xuan@yiad.am>
*
* Supervised for BJMUN Opearting System at 2022
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventtypes', function (Blueprint $table) {
            $table->string('id');
            $table->string('title');
            $table->string('text');
            $table->string('icon');
            $table->string('level');
            //$table->timestamps();
            $table->primary('id');
        });
		
		Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('eventtype_id');
            $table->integer('reg_id')->unsigned();
            $table->string('content');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('eventtype_id')->references('id')->on('eventtypes')->onDelete('no action');
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
        Schema::dropIfExists('eventtypes');
        Schema::dropIfExists('events');
    }
}
