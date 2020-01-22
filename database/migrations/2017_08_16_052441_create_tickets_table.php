<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('form_id')->unsigned()->index();
            $table->integer('employee_id')->unsigned()->index();

            $table->integer('technician_id')->nullable()->unsigned()->index();

           /*
            | Priority
            |------------------------------
            | Low = 0
            | Medium = 1
            | High = 2
            |
            */     
            $table->integer('priority')->default(0);

           /*
            | Status
            |------------------------------
            | Open = 0
            | On-hold = 1
            | Close = 2
            | Cancelled = 3
            |
            */
            $table->integer('status')->default(0);

           /*
            | State
            |------------------------------
            | On-going = 0
            | On-time = 1
            | Delayed = 2
            |
            */            
            $table->integer('state')->default(0);

            $table->datetime('start_date');
            $table->datetime('date_closed')->nullable();

            $table->boolean('hasViolation')->default(0);

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
        Schema::dropIfExists('tickets');
    }
}
