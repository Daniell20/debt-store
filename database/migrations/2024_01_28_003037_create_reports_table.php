<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('merchant_id');
            $table->text('description');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
