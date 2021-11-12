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

class CreateSeatassignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 本表用于提供席位分配权限。
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('seatassigners', function (Blueprint $table) {
            $table->integer('reg_id')->unsigned();
            $table->integer('committee_id')->unsigned();

            $table->foreign('reg_id')->references('id')->on('regs')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('committee_id')->references('id')->on('committees')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['reg_id', 'committee_id']);
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
        Schema::dropIfExists('seatassigners');
    }
}
