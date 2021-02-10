<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('associate_id')->index();
            $table->unsignedBigInteger('employee_id')->index()->nullable();
            $table->string('gender')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->double('equity_ratio', 18, 2)->nullable();
            $table->double('debt_ratio', 18, 2)->nullable();
            $table->double('equity_rate', 18, 2)->nullable();
            $table->double('debt_rate', 18, 2)->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('account_type')->nullable();
            $table->text('family_details')->nullable();
            $table->text('company_details')->nullable();
            $table->string('proceed_to')->nullable();
            $table->boolean('is_active')->default('0');
            $table->boolean('is_introduction')->default('0');
            $table->boolean('is_comprehensive')->default('0');
            $table->boolean('is_kyc_information')->default('0');
            $table->boolean('is_account_opened')->default('0');
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
        Schema::dropIfExists('client_accounts');
    }
}
