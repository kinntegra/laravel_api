<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managelogs', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->longText('logs')->nullable();
            $table->string('ip')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->morphs('managelogable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('managelogs');
    }
}
