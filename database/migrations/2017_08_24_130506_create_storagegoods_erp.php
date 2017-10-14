<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoragegoodsErp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storagegoods_erp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('storage_guid');
            $table->string('storageno');
            $table->string('goods_guid');
            $table->string('goodsno');
            $table->enum('status',['sync','new','invalid']);
            $table->timestamps();
            $table->unique(['storage_guid','goods_guid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storagegoods_erp');
    }
}
