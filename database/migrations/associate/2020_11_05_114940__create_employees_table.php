<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('associate_id')->index();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('department_id')->index();
            $table->unsignedBigInteger('subdepartment_id')->index();
            $table->unsignedBigInteger('designation_id')->index();
            $table->unsignedInteger('supervisor_id')->index();
            $table->unsignedInteger('profession_id')->index();
            $table->string('telephone')->nullable();
            $table->string('blood_group',10)->nullable();
            $table->text('health_issue')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('anniversary_date')->nullable();
            $table->boolean('is_active')->default('0');
            $table->boolean('is_credential_email')->default('0');
            $table->boolean('bse_upload')->default('0');
            $table->text('deactive_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('associate_id')->references('id')->on('associates');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('subdepartment_id')->references('id')->on('departments');
            $table->foreign('designation_id')->references('id')->on('designations');
            //$table->foreign('designation_id')->references('id')->on('designations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
