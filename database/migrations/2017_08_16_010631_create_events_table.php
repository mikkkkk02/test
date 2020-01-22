<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('event_category_id')->unsigned()->index();

            $table->string('title');
            $table->text('description');
            $table->string('facilitator');
            $table->string('color')->default('FFFFFF');
            $table->string('venue');
            $table->decimal('hours')->default(0);

            $table->integer('limit')->default(0);
            $table->integer('attending')->default(0);

            $table->date('start_date');
            $table->date('end_date');
            $table->datetime('registration_date');

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
 
            $table->boolean('isSameTime')->default(0);

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();
 
            $table->timestamps();
        });

        Schema::create('event_participants', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('event_id')->unsigned()->index();
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->bigInteger('participant_id')->unsigned()->index();
            $table->foreign('participant_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('form_id')->unsigned()->index();

           /*
            | Status
            |------------------------------
            | Pending = 0
            | Approved = 1
            | Disapproved = 2
            | Cancelled = 3
            |
            */
            $table->integer('status')->default(0);
            $table->string('reason')->nullable();

           /*
            | Status
            |------------------------------
            | Pending = 0
            | Attended = 1
            | No Show = 2
            |
            */
            $table->integer('hasAttended')->default(0);

            $table->integer('approver_id')->nullable()->unsigned()->index();
            $table->datetime('approved_at')->nullable(); 

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
        Schema::dropIfExists('event_participants');

        Schema::dropIfExists('events');
    }
}
