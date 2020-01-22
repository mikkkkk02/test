<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempTicketUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_ticket_updates', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('ticket_id')->unsigned()->index();
            $table->integer('employee_id')->unsigned()->index();

            $table->text('description');


            $table->integer('approver_id')->unsigned()->index();
            
            $table->integer('status')->default(0);
            $table->text('reason')->nullable();

            $table->datetime('approved_date')->nullable();


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
        Schema::dropIfExists('temp_ticket_updates');
    }
}
