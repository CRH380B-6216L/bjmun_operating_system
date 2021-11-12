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

class CreateConferenceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', array('init','daisreg','prep','reg','regstop','onhold','finish','cancelled'));
            $table->string('shortname')->default('BJMUN'); //代称，一律为 BJMUN
            $table->string('name'); //名称，例如 BJMUNC 2022
            $table->string('fullname'); //全名，例如 2022年北京市高中生模拟联合国大会
            $table->date('date_start');
            $table->date('date_end');
            $table->text('description');
            $table->timestamps();
        });
        
        Schema::create('committees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->string('name'); //短名称，例如 UNSC
            $table->string('display_name'); //全名，例如 联合国安全理事会
            $table->string('topic_0'); //默认议题，例如 阿富汗局势
            $table->string('topic_1')->nullable();
            $table->enum('topic_sel', ['Topic0', 'Topic1', 'Unchosen']);
            $table->enum('language', ['ChineseS', 'ChineseT', 'English']); //工作语言
            $table->string('rule'); //规则（暂不具备功能性，仅显示文本），例如 经北京模联修改的罗伯特议事规则
            $table->boolean('is_dual')->default(false); //是否双代
            $table->integer('capacity')->unsigned(); //定员，按代表人数计
            $table->integer('father_committee_id')->unsigned()->nullable(); //连接的父委员会ID
            $table->integer('delegategroup_id')->unsigned()->nullable();
            $table->integer('option_limit')->unsigned()->default(99); //用途未知
            $table->integer('maxAssignList')->default(1); //用途未知
            $table->enum('crisis', ['none', 'news_only', 'yes', 'joint']); //危机推动类型：无、仅新闻（不需代表反应）、有、有联动
            $table->date('timeframe_start');
            $table->date('timeframe_end'); //时间节点默认取会议起止日
            $table->boolean('is_allocated')->default(false); //是否下发了席位
            $table->integer('session')->unsigned(); //会期，与 EasyChair 联动使用
            $table->text('description');
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('father_committee_id')->references('id')->on('committees')->onDelete('set null');
            //$table->foreign('delegategroup_id')->references('id')->on('delegategroups')->onDelete('set null');
        });
        
        Schema::create('nations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned();
            $table->integer('committee_id')->unsigned();
            $table->string('name');
            $table->string('remark')->nullable(); //国家或席位备注
            $table->integer('conpetence')->unsigned()->default(1); //投票权重，默认均为 1，可根据规则或设定调整该值以变更权重，设为 0 则无投票权
            $table->boolean('veto_power')->default(false); //安理会一票否决权
            $table->boolean('attendance')->nullable(); //是否出席，与 EasyChair 联动
            $table->boolean('locked')->default(false); //席位分配锁定状态
            $table->enum('status', ['open', 'selected', 'locked'])->default('open'); //自选席位时的席位状态（开放，已被选择，已锁定）
            $table->timestamps();
            $table->unique(['committee_id', 'name']);
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('committee_id')->references('id')->on('committees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conferences');
        Schema::dropIfExists('committees');
        Schema::dropIfExists('nations');
    }
}
