<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictedMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restricted_merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger("merchant_id");
            $table->date("start_date");
            $table->date("end_date")->nullable();
            $table->text("reason");
            $table->timestamps();

            $table->foreign("merchant_id")->references("id")->on("merchants");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restricted_merchants');
    }
}
