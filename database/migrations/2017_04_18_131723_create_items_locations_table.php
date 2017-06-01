<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id');
            $table->integer('item_id');
            $table->string('comment')->nullable();
            $table->smallInteger('status'); // 0 - effective; 1 - ineffective,waiting delete
            $table->timestamps();
            $table->unique(array('location_id', 'item_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_locations');
    }
}
