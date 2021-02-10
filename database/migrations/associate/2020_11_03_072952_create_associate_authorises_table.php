<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateAuthorisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_authorises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_id')->index();
            $table->integer('aid')->nullable();
            $table->string('person')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_active')->default('1');
            $table->timestamps();

            $table->foreign('associate_id')->references('id')->on('associates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associate_authorises');
    }
}
