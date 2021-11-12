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

class CreateSchoolTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); //学校名
			$table->boolean('isSelected'); //是否理事校
            //$table->integer('reg_id')->unsigned();
            $table->enum('type', ['school', 'university'])->default('school'); //如果非理事校可能选择大学
            $table->string('description')->nullable(); //本校模联社自我绍介
            $table->enum('payment_method', ['individual', 'group']);
            //$table->string('joinCode')->nullable();
            $table->timestamps();
            //$table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
        });
		
		Schema::create('schooladmins', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('school_id')->unsigned();
            $table->integer('conference_id')->nullable()->unsigned(); // if null -> global school admin
            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
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
        Schema::dropIfExists('schools');
        Schema::dropIfExists('schooladmins');
    }
}
