<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id');
            $table->string('storage_guid');
            $table->string('goodsno');
            $table->string('goodsname');
            $table->string('goods_guid');
            $table->string('colorcode')->nullable();
            $table->string('colordesc')->nullable();
            $table->string('color_guid')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
            $table->unique(array('location_id','goodsno','colorcode'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_items');
    }
}
