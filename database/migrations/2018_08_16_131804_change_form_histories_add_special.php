<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFormHistoriesAddSpecial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_histories', function(Blueprint $table) {
            $table->text('purpose')->nullable();

           /*
            | @Special: For L&D
            |------------------------------
            */
            $table->boolean('isLocal')->default(1);
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
   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
