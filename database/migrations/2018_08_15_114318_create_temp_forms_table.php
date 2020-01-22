<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_forms', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('ticket_id')->unsigned()->index()->nullable();

            $table->integer('employee_id')->unsigned()->index();
            $table->integer('assignee_id')->nullable()->unsigned()->index();
            
            $table->text('purpose')->nullable();

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

           /*
            | @Special: For Meeting Room
            |------------------------------
            */
            $table->string('mr_title')->nullable();
            $table->date('mr_date')->nullable();
            $table->time('mr_start_time')->nullable();
            $table->time('mr_end_time')->nullable();              



            $table->integer('form_id')->unsigned()->index();
            $table->integer('requester_id')->unsigned()->index();

            $table->integer('status')->default(0);
            $table->text('reason')->nullable();

            $table->datetime('approved_date')->nullable();


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
        Schema::dropIfExists('temp_forms');
    }
}
