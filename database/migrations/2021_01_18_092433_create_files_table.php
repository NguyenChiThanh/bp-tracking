<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('creator_id')->nullable()->comment('The user uploaded file');
            $table->string('name')->comment('File name used for displaying in UI');
            $table->string('file')->comment('File name stored in server');
            $table->string('disk')->default('public')->comment('Config in filesystem');
            $table->string('url_path')->nullable()->comment('URL link for browser read file');
            $table->string('disk_path')->comment('Relative link for server read file');
            $table->string('type')->nullable()->comment('File type');
            $table->unsignedBigInteger('size')->nullable()->comment('The size of file in bytes');
            $table->string('extension')->nullable();
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
        Schema::dropIfExists('files');
    }
}
