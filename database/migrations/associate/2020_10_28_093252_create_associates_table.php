<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('associate_code', 50)->nullable();
            $table->string('bse_password')->nullable();
            $table->integer('introducer_id')->index()->nullable();
            $table->integer('employee_id')->index()->nullable();
            $table->unsignedBigInteger('profession_id')->index();
            $table->string('business_tag',50)->nullable();
            $table->unsignedBigInteger('entitytype_id')->index()->nullable();
            $table->string('entity_name')->nullable();
            $table->date('birth_incorp_date')->nullable();
            $table->string('telephone')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('gst_no',50)->nullable();
            $table->date('gst_validity')->nullable();
            $table->string('shop_est_no',50)->nullable();
            $table->date('shop_est_validity')->nullable();
            $table->string('primary_color', 50)->nullable();
            $table->string('secondary_color', 50)->nullable();
            $table->boolean('is_active')->default('0');
            $table->boolean('is_credential_email')->default('0');
            $table->boolean('bse_upload')->default('0');
            $table->text('deactive_reason')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('profession_id')->references('id')->on('professions');

            //$table->foreign('address_id')->references('id')->on('addresses');
            //$table->foreign('bank_id')->references('id')->on('banks');
            //$table->integer('gst_upload')->nullable();
            //$table->integer('shop_est_upload')->nullable();
            // $table->integer('pd_upload')->nullable();
            // $table->integer('pd_asl_upload')->nullable();
            // $table->integer('pd_coi_upload')->nullable();
            // $table->integer('co_moa_upload')->nullable();
            // $table->integer('co_aoa_upload')->nullable();
            // $table->integer('co_coi_upload')->nullable();
            // $table->integer('co_asl_upload')->nullable();
            // $table->integer('co_br_upload')->nullable();
            //$table->unsignedBigInteger('address_id')->index();
            //$table->unsignedBigInteger('bank_id')->index();
            //$table->integer('logo_upload')->nullable();
            //$table->integer('aadhar_upload')->nullable();
            //$table->integer('pan_upload')->nullable();
            //$table->integer('photo_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associates');
    }
}
