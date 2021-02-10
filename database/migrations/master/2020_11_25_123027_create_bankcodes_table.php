<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bankcodes', function (Blueprint $table) {
            $table->id();
            $table->string('bank')->nullable();
            $table->string('ifsc')->index()->nullable();
            $table->string('micr_code')->index()->nullable();
            $table->string('branch')->nullable();
            $table->string('address')->nullable();
            $table->string('std_code')->nullable();
            $table->string('contact')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
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
        Schema::dropIfExists('bankcodes');
    }
}
