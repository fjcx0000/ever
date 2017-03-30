<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSizevalues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sizevalues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sizetype_id', 5);
            $table->string('size_value', 10);
            $table->string('description')->nullable();
            $table->unique(array('sizetype_id','size_value'));
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
        Schema::dropIfExists('sizevalues');
    }
}
