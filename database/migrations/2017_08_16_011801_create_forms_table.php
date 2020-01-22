<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('ticket_id')->unsigned()->index()->nullable();
            
            $table->integer('employee_id')->unsigned()->index();
            
            $table->text('purpose')->nullable();

           /*
            | Status
            |------------------------------
            | Pending = 0
            | Approved = 1
            | Disapproved = 2
            | Draft = 3
            |
            */
            $table->integer('status')->default(0);

           /*
            | @Special: For L&D
            |------------------------------
            */
            $table->decimal('course_cost', 19, 4)->default(0);
            $table->decimal('accommodation_cost', 19, 4)->default(0);
            $table->decimal('meal_cost', 19, 4)->default(0);
            $table->decimal('transport_cost', 19, 4)->default(0);
            $table->decimal('others_cost', 19, 4)->default(0);
            $table->decimal('total_cost', 19, 4)->default(0);


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
        Schema::dropIfExists('forms');
    }
}
