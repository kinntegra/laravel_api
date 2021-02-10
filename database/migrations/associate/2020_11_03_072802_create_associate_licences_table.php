<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateLicencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_licences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_id')->index();
            $table->string('arn_name')->nullable();
            $table->string('arn_rgn_no', 50)->nullable();
            $table->date('arn_validity')->nullable();
            $table->string('euin_name')->nullable();
            $table->string('euin_no', 50)->nullable();
            $table->date('euin_validity')->nullable();
            $table->string('ria_name')->nullable();
            $table->string('ria_rgn_no', 50)->nullable();
            $table->date('ria_validity')->nullable();
            $table->timestamps();

            $table->foreign('associate_id')->references('id')->on('associates');

            //$table->integer('arn_upload')->nullable();
            //$table->integer('euin_upload')->nullable();
            //$table->integer('ria_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associate_licences');
    }
}
