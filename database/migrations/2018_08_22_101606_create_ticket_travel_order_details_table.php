<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTravelOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_travel_order_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('ticket_id')->unsigned()->index();
            $table->integer('form_template_field_id')->unsigned()->index()->nullable();

            $table->dateTime('date_of_travel_from')->nullable();
            $table->string('destination_from')->nullable();
            $table->string('destination_to')->nullable();
            $table->string('accommodation');

            $table->string('transportation_type');

            $table->longText('details')->nullable();
            $table->string('remarks')->nullable();
            
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
        Schema::dropIfExists('ticket_travel_order_details');
    }
}
