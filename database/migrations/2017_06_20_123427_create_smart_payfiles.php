<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmartPayfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smart_payfiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('filename');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('rec_number');
            $table->smallInteger('check_flag')->default(0); // 0-not check;1-checked ok;2-checked and error exists
            $table->timestamps();

            $table->unique(['filename']);
            $table->index(['start_date','end_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smart_payfile');
    }
}
