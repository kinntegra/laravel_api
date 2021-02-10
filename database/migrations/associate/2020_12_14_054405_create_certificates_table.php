<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('associate_id')->index();
            $table->string('nism_va_no', 50)->nullable();
            $table->date('nism_va_validity')->nullable();
            $table->string('ria_certificate_type')->nullable();
            $table->string('nism_xa_no', 50)->nullable();
            $table->date('nism_xa_validity')->nullable();
            $table->string('nism_xb_no', 50)->nullable();
            $table->date('nism_xb_validity')->nullable();
            $table->string('cfp_no', 50)->nullable();
            $table->date('cfp_validity')->nullable();
            $table->string('cwm_no', 50)->nullable();
            $table->date('cwm_validity')->nullable();
            $table->string('ca_no', 50)->nullable();
            $table->date('ca_validity')->nullable();
            $table->string('cs_no', 50)->nullable();
            $table->date('cs_validity')->nullable();
            $table->string('course_name')->nullable();
            $table->string('course_no', 50)->nullable();
            $table->date('course_validity')->nullable();
            $table->timestamps();
            $table->morphs('certificateable');
            //$table->foreign('associate_id')->references('id')->on('associates');

            //$table->integer('nism_va_upload')->nullable();
            //$table->integer('nism_xa_upload')->nullable();
            //$table->integer('nism_xb_upload')->nullable();
            //$table->integer('cfp_upload')->nullable();
            //$table->integer('cwm_upload')->nullable();
            //$table->integer('ca_upload')->nullable();
            //$table->integer('cs_upload')->nullable();
            //$table->integer('course_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
