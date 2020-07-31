<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('from_ts');
            $table->unsignedBigInteger('to_ts');
            $table->unsignedBigInteger('buffer_ts');


            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');

            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions');

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
        Schema::dropIfExists('bookings');
    }
}
