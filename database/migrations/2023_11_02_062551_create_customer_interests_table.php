<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_interests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("customer_id");
            $table->bigInteger("debt_id");
            $table->bigInteger("interest_rate");
            $table->date("calculation_date");
            $table->bigInteger('calculated_interest_amount');
            $table->bigInteger('debt_status_id');
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
        //
    }
}
