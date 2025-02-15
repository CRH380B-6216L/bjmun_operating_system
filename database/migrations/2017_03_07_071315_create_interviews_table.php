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
* Supervised for BJMUN Operating System at 2022
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->integer('reg_id')->unsigned();
            $table->integer('interviewer_id')->nullabled()->unsigned();
            $table->dateTime('arranged_at')->nullable();
            $table->text('arranging_notes')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->enum('status', ['assigned', 'arranged', 'cancelled', 'passed', 'failed', 'undecided', 'retest', 'exempted']);
            $table->boolean('retest')->default(false);
            $table->float('score')->nullable(); // Total
            $table->text('internal_fb')->nullable();
            $table->text('public_fb')->nullable();
            $table->text('scores')->nullable(); // JSON
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('interviewer_id')->references('id')->on('regs')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interviews');
    }
}
