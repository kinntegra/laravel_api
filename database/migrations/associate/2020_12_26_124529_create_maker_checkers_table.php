<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMakerCheckersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maker_checkers', function (Blueprint $table) {
            $table->id();
            $table->integer('maker_id')->index()->nullable();
            $table->integer('checker_id')->index()->nullable();
            $table->unsignedBigInteger('status_id')->index();
            $table->text('maker_comment')->nullable();
            $table->text('admin_comment')->nullable();
            $table->boolean('is_accept_by_checker')->default('0');
            $table->boolean('is_reject_by_checker')->default('0');
            $table->text('checker_reject_reason')->nullable();
            $table->boolean('is_accept_by_user')->default('0');
            $table->boolean('is_reject_by_user')->default('0');
            $table->text('user_reject_reason')->nullable();
            $table->timestamps();
            $table->morphs('makercheckerable');

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
        Schema::dropIfExists('maker_checkers');
    }
}
