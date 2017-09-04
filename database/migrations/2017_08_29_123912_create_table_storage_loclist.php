<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStorageLoclist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_loclist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area');
            $table->string('line');
            $table->string('unit');
            $table->string('locname');
            $table->smallInteger('levels');
            $table->timestamps();
            $table->unique(array('area','line','unit'));
            $table->unique('locname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_loclist');
    }
}
