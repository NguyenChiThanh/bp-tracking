<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contract_code');
            $table->string('license_code');
            $table->string('status')->nullable(); // running ( from_ts <= curr_time <= to_ts) - reserved (to_ts > curr_time ) - finish (to_ts < curr_time)

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands');

            $table->unsignedBigInteger('from_ts');
            $table->unsignedBigInteger('to_ts');

            $table->json('position_list')->nullable();
            $table->integer('days_diff');
            $table->double('position_price');

            $table->string('discount_type')->nullable();
            $table->double('discount_value')->nullable();
            $table->double('discount_max')->nullable();
            $table->double('total_discount')->nullable();

            $table->double('total_price');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');

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
        Schema::dropIfExists('campaigns');
    }
}
