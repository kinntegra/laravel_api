<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateNomineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_nominees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_id')->index();
            $table->string('nominee_name')->nullable();
            $table->date('nominee_birth_date')->nullable();
            $table->boolean('is_minor')->default('0');
            $table->boolean('is_primary_address')->default('0');
            $table->string('nominee_mobile')->nullable();
            $table->string('nominee_telephone')->nullable();
            $table->string('nominee_email')->nullable();
            $table->string('nominee_primary_address')->nullable();
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
        Schema::dropIfExists('associate_nominees');
    }
}
