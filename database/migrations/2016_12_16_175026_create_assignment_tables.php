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

class CreateAssignmentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		//本表用于定义需要代表或候选人完成的学术作业
        Schema::create('assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->enum('subject_type', ['individual', 'nation', 'partner']);//定义提交主体类别：个人、席位（组）、搭档
            $table->enum('handin_type', ['upload', 'text', 'form']);//定义提交内容类别：上传文件、文本、表单
            $table->boolean('reg_assignment')->default(false);
            $table->string('title');
            $table->mediumText('description');
            $table->dateTime('deadline');
            $table->timestamps();
            $table->unique(['title', 'conference_id']);
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
        
		//本表用于保存代表上交的学术作业
        Schema::create('handins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reg_id')->unsigned(); //无论何种提交类型，均强制记录提交者
            $table->integer('nation_id')->nullable()->unsigned(); //对非国家单位的学测可留空
            $table->integer('assignment_id')->unsigned();
            $table->enum('handin_type', ['upload', 'text', 'json']); //If upload, assignment_content = file location
            $table->boolean('confirm');
            $table->mediumtext('content');
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('nation_id')->references('id')->on('nations')->onDelete('no action');
            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
        });
		
		//本表用于连接表单类别学术作业所使用的表单
		Schema::create('assignment_form', function (Blueprint $table) {
            $table->integer('assignment_id')->unsigned();
            $table->integer('form_id')->unsigned();
            $table->foreign('assignment_id')->references('id')->on('assignments')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('form_id')->references('id')->on('forms')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['assignment_id', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('handins');
        Schema::dropIfExists('assignment_form');
    }
}
