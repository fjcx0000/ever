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
            $table->string('goods_guid');
            $table->enum('status',['sync','new','invalid']);
            $table->timestamps();
            $table->unique(array('storage_guid','goods_guid'));
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
