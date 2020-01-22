<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFormsAddMeeting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function(Blueprint $table) {
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
