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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles 设定身份类型
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned()->nullable();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onUpdate('cascade')->onDelete('set null');
        });

        // Create table for associating roles to users (Many-to-Many) 设定身份类型信息（以参会身份为单位）
        Schema::create('reg_role', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('reg_id')->references('id')->on('regs')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['reg_id', 'role_id']);
        });

        // Create table for storing permissions 设定各种系统权限
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many) 设定身份类型所拥有之系统权限
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
        Schema::drop('reg_role');
        Schema::drop('roles');
    }
}
