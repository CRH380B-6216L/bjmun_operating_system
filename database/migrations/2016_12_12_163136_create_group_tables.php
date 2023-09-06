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

class CreateGroupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * 创建席位分组和代表分组，用于向特定组别人员发放作业、文件等内容
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nationgroups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->string('name'); //内部名
            $table->string('display_name'); //代表可见名
            // TODO: 根据需要添加列，例如“标记颜色”等
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
        
        Schema::create('delegategroups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->string('name');
            $table->string('display_name');
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        }); 
        
        Schema::table('committees', function (Blueprint $table) {
            $table->foreign('delegategroup_id')->references('id')->on('delegategroups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropForeign('delegategroup_id_foreign');
        });
        Schema::dropIfExists('nationgroups');
        Schema::dropIfExists('delegategroups');
    }
}
