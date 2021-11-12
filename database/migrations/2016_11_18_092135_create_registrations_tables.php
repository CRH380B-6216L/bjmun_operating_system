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

class CreateRegistrationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //本表为参会者身份表，与用户表不同的是，该表内数据代表用户在某次特定会议内的各类信息。参会者身份表有 'ot', 'dais', 'teamadmin', 'delegate', 'observer', 'volunteer', 'interviewer' 六个子表，分别代表六种参会身份。
        Schema::create('regs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('conference_id')->unsigned()->nullable(); // null -> global things
            $table->integer('school_id')->unsigned()->nullable();
            $table->string('order_id')->nullable(); //与该身份连接的会费订单
            $table->enum('type', ['unregistered', 'ot', 'dais', 'teamadmin', 'delegate', 'observer', 'volunteer', 'interviewer']);
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('password')->nullable(); //独立密码（旧系统时代未使用）
            $table->boolean('enabled'); //该报名身份有效性（false 时无效并锁定）
            $table->boolean('accomodate')->nullable(); //是否住宿
            $table->integer('roommate_user_id')->nullable()->unsigned(); //通过室友配对系统查找到或分配的室友 ID
            $table->text('reginfo')->nullable(); //一些备注
            $table->timestamps();
            $table->unique(['user_id', 'conference_id', 'type']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('roommate_user_id')->references('id')->on('users')->onDelete('set null');
            //$table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
        
        Schema::create('delegate_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('school_id')->unsigned()->nullable();
            $table->enum('status', ['reg','sVerified', 'oVerified', 'unpaid', 'paid', 'fail'])->default('reg'); //报名状态（已报名、学校确认、组织团队确认、等待缴费、已缴费、报名失败）
            $table->integer('committee_id')->unsigned()->nullable();
            $table->integer('nation_id')->nullable()->unsigned();
            $table->integer('partner_reg_id')->nullable()->unsigned();
            $table->boolean('seat_locked')->default(false);
            $table->timestamps();
            $table->primary('reg_id');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('committee_id')->references('id')->on('committees')->onDelete('set null');
            $table->foreign('nation_id')->references('id')->on('nations')->onDelete('set null');
            $table->foreign('partner_reg_id')->references('id')->on('regs')->onDelete('set null');
        });
        
        Schema::create('volunteer_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('school_id')->unsigned()->nullable();
            $table->enum('status', ['reg','sVerified', 'oVerified', 'unpaid', 'paid', 'fail'])->default('reg'); //报名状态（已报名、学校确认、组织团队确认、等待缴费、已缴费、报名失败）
            $table->timestamps();
            $table->primary('reg_id');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
        
        Schema::create('observer_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('school_id')->unsigned()->nullable();
            $table->enum('status', ['reg','sVerified', 'oVerified', 'unpaid', 'paid', 'fail'])->default('reg'); //报名状态（已报名、学校确认、组织团队确认、等待缴费、已缴费、报名失败）
            $table->timestamps();
            $table->primary('reg_id');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
        
        Schema::create('dais_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('school_id')->unsigned()->nullable();
            $table->integer('committee_id')->unsigned();
            $table->enum('status', ['reg', 'oVerified', 'success', 'fail'])->default('reg');//报名状态（已报名、组织团队确认、录取成功、录取失败）
            $table->string('position');//学术团队成员身份，如 学术负责人
            $table->text('handin')->nullable();//学术作业内容，尚未确认是否有 JSON 等复杂形式
            $table->timestamps();
            $table->primary('reg_id');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('committee_id')->references('id')->on('committees')->onDelete('cascade');
        });
        
        Schema::create('ot_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned()->nullable();
            $table->integer('school_id')->unsigned()->nullable();
            $table->enum('status', ['reg', 'oVerified', 'success', 'fail'])->default('reg'); //报名状态（已报名、组织团队确认、录取成功、录取失败）
            $table->string('position');//组织团队成员身份，如 秘书长
            $table->text('handin')->nullable(); //学术作业内容，尚未确认是否有 JSON 等复杂形式
            $table->timestamps();
            $table->primary('reg_id');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
        
        Schema::create('interviewer_info', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('conference_id')->unsigned();
            $table->integer('committee_id')->nullable()->unsigned();
            $table->primary('reg_id');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('reg_id')->references('id')->on('regs')->onDelete('cascade');
            $table->foreign('committee_id')->references('id')->on('committees')->onDelete('cascade');
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
        Schema::dropIfExists('regs');
        Schema::dropIfExists('delegate_info');      
        Schema::dropIfExists('volunteer_info');
        Schema::dropIfExists('observer_info');
        Schema::dropIfExists('dais_info');
        Schema::dropIfExists('ot_info');
        Schema::dropIfExists('interviewer_info');
    }
}
