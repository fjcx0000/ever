<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmartPayrecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smart_payrecords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sc_sku');
            $table->string('international_sku');
            $table->string('source_order_id');
            $table->date('date');
            $table->string('qty');
            $table->decimal('price',10,2);
            $table->decimal('amount',10,2);

            // 0: not checked; 1: checked ok; 2: order not exist in the orders;3:qty or item wrong
            $table->smallInteger('check_flag')->default(0);
            $table->integer('file_id');

            $table->timestamps();

            $table->unique(['file_id','date','source_order_id','sc_sku']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smart_payrecords');
    }
}
