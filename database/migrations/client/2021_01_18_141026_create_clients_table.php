<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->integer('ucc')->unsigned()->nullable();
            $table->string('ucc_old')->nullable();
            $table->integer('sr_no')->nullable();
            $table->string('account_type')->nullable();
            $table->boolean('has_nominee')->nullable();
            $table->integer('client_profiles_id')->nullable();
            $table->string('client_profiles_name')->nullable();
            $table->string('relationship')->nullable();
            $table->string('guardian_name')->nullable();
            $table->boolean('bse_export_status')->nullable();
            $table->date('bse_export_date')->nullable();
            $table->string('aof_file_name')->nullable();
            $table->string('aof_pdf_file_name')->nullable();
            $table->boolean('is_aof_uploaded')->nullable();
            $table->date('aof_upload_date')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->boolean('is_verified_two')->nullable();
            $table->string('confirmation_link')->nullable();
            $table->dateTime('confirmation_valid_till')->nullable();
            $table->dateTime('confirmation_accepted_on')->nullable();
            $table->string('client_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
