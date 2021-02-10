<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateCommercialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_commercials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_id')->index();
            $table->unsignedBigInteger('commercial_id')->index();
            $table->unsignedBigInteger('commercialtype_id')->index();
            $table->double('commercial', 16, 2)->nullable();
            $table->timestamps();

            $table->foreign('associate_id')->references('id')->on('associates');
            $table->foreign('commercial_id')->references('id')->on('commercials');
            $table->foreign('commercialtype_id')->references('id')->on('commercialtypes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associate_commercials');
    }
}
