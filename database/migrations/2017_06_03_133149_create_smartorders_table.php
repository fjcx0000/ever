<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmartordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smartorders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer')->nullable();
            $table->string('company')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('sc_sku');
            $table->string('international_sku');
            $table->string('qty');
            $table->string('item_des');
            $table->string('order_instructions')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('order_id');
            $table->date('order_date');
            $table->string('source_name')->nullable();
            $table->string('source_order_id');

            // true: despatched false: not despatched
            $table->boolean('isDespatched')->default(TRUE);
            // 0: not checked; 1: checked ok; 2: order not exist in the payment list;3:qty or item wrong
            $table->smallInteger('check_flag')->default(0);
            $table->string('order_filename');
            $table->date('file_date');
            $table->string('comments')->nullable();
            $table->string('product_id', 20);
            $table->string('color')->nullable();
            $table->string('size_value', 10)->nullable();

            $table->timestamps();

            $table->unique(['order_date','order_id','sc_sku']);
            $table->index(['file_date','order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smartorders');
    }
}
