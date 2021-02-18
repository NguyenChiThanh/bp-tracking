<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('file_name')->unique();
            $table->string('name')->nullable();
            $table->text('current_step')->nullable();
            $table->unsignedTinyInteger('imported')->default(0);
            $table->string('type')->default('asset');
            $table->integer('rows_imported')->default(0);
            $table->integer('total_rows')->default(0);
            $table->text('message')->nullable();
            $table->string('language', 16)->default('vi');
            $table->json('params')->nullable();
            $table->json('result')->nullable();
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
        Schema::dropIfExists('utilities');
    }
}
