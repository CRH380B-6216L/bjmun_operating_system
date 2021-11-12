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
class CreateRelationshipTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //关系表：席位组（分组）—席位（对象）
        Schema::create('nationgroup_nation', function (Blueprint $table) {
            $table->integer('nationgroup_id')->unsigned();
            $table->integer('nation_id')->unsigned();
            $table->foreign('nationgroup_id')->references('id')->on('nationgroups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nation_id')->references('id')->on('nations')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['nationgroup_id', 'nation_id']);
        });
        
        //关系表：代表组（分组）—代表（对象）
        Schema::create('delegate_delegategroup', function (Blueprint $table) {
            $table->integer('delegategroup_id')->unsigned();
            $table->integer('delegate_id')->unsigned();
            $table->foreign('delegategroup_id')->references('id')->on('delegategroups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('delegate_id')->references('reg_id')->on('delegate_info')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['delegategroup_id', 'delegate_id']);
        });
        
        //关系表：学术作业（内容）—席位组（分组）
        Schema::create('assignment_nationgroup', function (Blueprint $table) {
            $table->integer('assignment_id')->unsigned();
            $table->integer('nationgroup_id')->unsigned();
            $table->foreign('assignment_id')->references('id')->on('assignments')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nationgroup_id')->references('id')->on('nationgroups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['assignment_id', 'nationgroup_id']);
        });
        
        //关系表：学术作业（内容）—代表组（分组）
        Schema::create('assignment_delegategroup', function (Blueprint $table) {
            $table->integer('assignment_id')->unsigned();
            $table->integer('delegategroup_id')->unsigned();
            $table->foreign('assignment_id')->references('id')->on('assignments')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('delegategroup_id')->references('id')->on('delegategroups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['assignment_id', 'delegategroup_id']);
        });
        
        //关系表：学术作业（内容）—委员会（分组）
        Schema::create('assignment_committee', function (Blueprint $table) {
            $table->integer('assignment_id')->unsigned();
            $table->integer('committee_id')->unsigned();
            $table->foreign('assignment_id')->references('id')->on('assignments')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('committee_id')->references('id')->on('committees')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['assignment_id', 'committee_id']);
        });
        
        //关系表：学术作业（内容）—席位组（分组）
        Schema::create('document_nationgroup', function (Blueprint $table) {
            $table->integer('document_id')->unsigned();
            $table->integer('nationgroup_id')->unsigned();
            $table->foreign('document_id')->references('id')->on('documents')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nationgroup_id')->references('id')->on('nationgroups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['document_id', 'nationgroup_id']);
        });
        
        //关系表：学术文件（内容）—代表组（分组）
        Schema::create('delegategroup_document', function (Blueprint $table) {
            $table->integer('document_id')->unsigned();
            $table->integer('delegategroup_id')->unsigned();
            $table->foreign('document_id')->references('id')->on('documents')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('delegategroup_id')->references('id')->on('delegategroups')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['document_id', 'delegategroup_id']);
        });
        
        //关系表：学术文件（内容）—委员会（分组）
        Schema::create('committee_document', function (Blueprint $table) {
            $table->integer('committee_id')->unsigned();
            $table->integer('document_id')->unsigned();
            $table->foreign('committee_id')->references('id')->on('committees')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['document_id', 'committee_id']);
        });
        
        //关系表：席位（对象）—代表（对象）
        Schema::create('delegate_nation', function (Blueprint $table) {
            $table->integer('delegate_id')->unsigned();
            $table->integer('nation_id')->unsigned();

            $table->foreign('delegate_id')->references('reg_id')->on('delegate_info')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nation_id')->references('id')->on('nations')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['delegate_id', 'nation_id']);
            $table->timestamps();
        });
        
        //关系表：学校（对象）—用户（对象）
        Schema::create('school_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('school_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['user_id', 'school_id']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nationgroup_nation');
        Schema::dropIfExists('delegate_delegategroup');
        Schema::dropIfExists('assignment_nationgroup');
        Schema::dropIfExists('assignment_delegategroup');
        Schema::dropIfExists('assignment_committee');
        Schema::dropIfExists('document_nationgroup');
        Schema::dropIfExists('delegategroup_document');
        Schema::dropIfExists('committee_document');
        Schema::dropIfExists('delegate_nation');
        Schema::dropIfExists('school_user');
    }
}