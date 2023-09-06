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

class CreateShoppingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conference_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('image');
            $table->string('options');
            $table->boolean('enabled')->default(false);
            $table->double('price');
            $table->integer('remains'); //-1 for unlimited
            $table->timestamps();
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
        
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id');
            $table->integer('user_id')->unsigned();
            $table->integer('conference_id')->unsigned()->nullable();
            $table->enum('status', ['unpaid', 'paid', 'done', 'cancelled'])->default('unpaid');
            $table->enum('shipment_method', ['mail', 'conference', 'none'])->default('none'); //快递；会议领取；虚拟商品
            $table->string('address')->nullable();
            $table->string('shipment_no')->nullable();
            $table->text('content'); //JSON; string sucks
            /*** TEEGON ***/
            $table->string('charge_id')->nullable();//流水号
            $table->string('buyer')->nullable();//支付方(微信ID/支付宝手机号)
            $table->string('payment_no')->nullable();//第三方交易单号
            /*** TEEGON ***/
            $table->double('price');//冗余，空间换时间
            $table->string('payment_channel')->nullable();//wxpay, alipay  为方便扩展，天工可能开发新接口如Apple Pay等，不做成enum
            $table->dateTime('payed_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->timestamps();
            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('regs')->onDelete('no action');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('no action');
        });
        
        Schema::create(config('cart.database.table'), function (Blueprint $table) {
            $table->string('identifier');
            $table->string('instance');
            $table->longText('content');
            $table->nullableTimestamps();
            $table->primary(['identifier', 'instance']);
        });
		        
        Schema::table('regs', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regs', function (Blueprint $table) {
            $table->dropForeign('order_id_foreign');
        });
        Schema::dropIfExists('goods');
        Schema::dropIfExists('orders');
        Schema::drop(config('cart.database.table'));
    }
}
