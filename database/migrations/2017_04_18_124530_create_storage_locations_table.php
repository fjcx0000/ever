<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area');
            $table->string('line');
            $table->string('unit');
            $table->string('level')->nullable();
            $table->string('storage_guid');
            $table->string('storageno');
            $table->enum('status',['sync','new','invalid']);
            $table->timestamps();
            $table->unique(array('area','line','unit','level'));
            $table->unique('storage_guid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_locations');
    }
}
