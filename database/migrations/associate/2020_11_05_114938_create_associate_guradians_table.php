<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateGuradiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_guradians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_nominee_id')->index();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_pan_no')->nullable();
            $table->string('guardian_nominee_relation', 50)->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_telephone')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('guardian_primary_address')->nullable();
            $table->timestamps();

            $table->foreign('associate_nominee_id')->references('id')->on('associate_nominees')->onDelete('cascade');;
            //$table->integer('guardian_pan_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associate_guradians');
    }
}
