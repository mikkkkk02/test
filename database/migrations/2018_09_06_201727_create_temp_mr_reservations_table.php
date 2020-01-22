<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempMrReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_mr_reservations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('location_id')->unsigned()->index()->nullable();
            $table->integer('room_id')->unsigned()->index()->nullable();
            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('form_id')->unsigned()->index();
            
            $table->integer('mr_reservation_id')->unsigned()->index()->nullable();

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->default('FFFFFF');

            $table->integer('status')->default(0);

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
        Schema::dropIfExists('temp_mr_reservations');
    }
}