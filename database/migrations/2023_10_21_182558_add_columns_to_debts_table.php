<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->bigInteger('amount_paid')->after("amount");
            $table->integer('debt_status_id')->after("amount_paid");
            $table->boolean("is_claimed")->after("debt_status_id")->default(false);
            $table->bigInteger('current_amount')->after("debt_status_id");
            $table->date('product_price_change_date')->after("current_amount");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            //
        });
    }
}
