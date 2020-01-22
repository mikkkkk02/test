<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormHistoryMrReservationTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_history_mr_reservation_times', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('mr_reservation_id')->unsigned()->index();

            $table->date('date');
            
            $table->time('start_time');
            $table->time('end_time');

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();

            $table->softDeletes();
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
        Schema::dropIfExists('form_history_mr_reservation_times');
    }
}
