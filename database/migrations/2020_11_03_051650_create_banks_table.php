<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('ifsc_no')->nullable();
            //$table->integer('cheque_upload')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('micr')->nullable();
            $table->string('account_type')->nullable();
            $table->string('account_no')->nullable();
            $table->boolean('is_active')->default('1');
            $table->boolean('is_default')->default('1');
            $table->timestamps();
            $table->softDeletes();
            $table->morphs('bankable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}
