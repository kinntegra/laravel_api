<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMakerCheckerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maker_checker_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maker_checker_id')->index();
            $table->integer('user_id')->nullable();
            $table->unsignedBigInteger('status_id')->index();
            $table->text('user_comment')->nullable();
            $table->timestamps();

            $table->foreign('maker_checker_id')->references('id')->on('maker_checkers');
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maker_checker_logs');
    }
}
