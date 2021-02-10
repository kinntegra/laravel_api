<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('relation')->nullable();
            $table->string('name')->nullable();
            $table->string('pan')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('tax_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('gross_annual_income')->nullable();
            $table->string('politically_exposed')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('aadhar')->nullable();
            $table->string('country_code')->nullable();
            // $table->string('mobile');
            // $table->string('email');
            // $table->string('correspondence_address1');
            // $table->string('correspondence_address2');
            // $table->string('correspondence_address3');
            // $table->string('correspondence_city');
            // $table->string('correspondence_state');
            // $table->string('correspondence_country');
            // $table->string('correspondence_pincode');
            // $table->string('permanent_address1');
            // $table->string('permanent_address2');
            // $table->string('permanent_address3');
            // $table->string('permanent_city');
            // $table->string('permanent_state');
            // $table->string('permanent_country');
            // $table->string('permanent_pincode');
            // $table->string('sign_image_file_name');
            // $table->string('address_image_file_name');
            // $table->string('faddress_image_file_name');
            // $table->string('pan_image_file_name');
            // $table->string('bank_image_file_name');
            $table->string('wealth_source')->nullable();
            $table->boolean('is_fatca_uploaded')->nullable();
            $table->integer('retirement_age')->nullable();
            $table->integer('life_expectancy')->nullable();
            $table->integer('tax_slab')->nullable();
            $table->double('expense_weightage', 18, 2)->nullable();
            $table->double('equity_ratio', 18, 2)->nullable();
            $table->double('debt_ratio', 18, 2)->nullable();
            $table->double('equity_rate', 18, 2)->nullable();
            $table->double('debt_rate', 18, 2)->nullable();
            $table->boolean('is_account_profile')->nullable();
            $table->integer('client_guardian_id')->nullable();
            // $table->string('birth_image_file_name');
            // $table->string('other_image_file_name');
            $table->double('equity_ratio_sip', 18, 2)->nullable();
            $table->double('debt_ratio_sip', 18, 2)->nullable();
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_profiles');
    }
}
