<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLearningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learnings', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('learning_category_id')->unsigned()->index();

            $table->string('title');
            $table->text('description');
            $table->string('venue');
            $table->string('facilitator');
            $table->string('provider');
            $table->decimal('hours')->default(0);            

            $table->date('start_date');
            $table->date('end_date');

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();            

            $table->boolean('isSameTime')->default(0);

           /*
            | Type
            |------------------------------
            | Individual Development Plan = 0
            | Mandatory Programs = 1
            | Others = 2
            |
            */            
            $table->integer('type')->default(0);
            $table->string('type_others')->nullable();

            $table->string('objective');
            $table->string('application');

            $table->decimal('course_fee', 8, 2);
            $table->decimal('accommodation_fee', 8, 2);
            $table->decimal('meal_fee', 8, 2);
            $table->decimal('transport_fee', 8, 2);
            $table->decimal('other_fee', 8, 2);
            $table->decimal('total_fee', 8, 2);
            $table->string('cost_center');

           /*
            | Type
            |------------------------------
            | In order = 0
            | Simultaneously = 1
            |
            */            
            $table->integer('approval_option')->default(0);

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();

            $table->timestamps();
        });

        Schema::create('learning_participants', function(Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->index();

            $table->integer('learning_id')->unsigned()->index();
            $table->foreign('learning_id')
                ->references('id')
                ->on('learnings')
                ->onDelete('cascade');

            $table->bigInteger('participant_id')->unsigned()->index();
            $table->foreign('participant_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('form_id')->unsigned();

            $table->integer('charge_to')->unsigned()->index();

           /*
            | Status
            |------------------------------
            | Pending = 0
            | Approved = 1
            | Disapproved = 2
            |
            */            
            $table->integer('status')->default(0);
            $table->boolean('hasAttended')->default(0);

            $table->integer('approver_id')->nullable()->unsigned()->index();
            $table->datetime('approved_at')->nullable();
        });         
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_participants');

        Schema::dropIfExists('learnings');
    }
}
