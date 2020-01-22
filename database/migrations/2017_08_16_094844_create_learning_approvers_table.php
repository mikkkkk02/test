<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLearningApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_approvers', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('learning_id')->unsigned()->index();
            $table->integer('approver_id')->unsigned()->index();     
            
           /*
            | Type
            |------------------------------
            | Immediate Leader = 0
            | Next Level Leader = 1
            | Employee = 2
            |
            */ 
            $table->integer('type')->default(0);            

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
        Schema::dropIfExists('learning_approvers');
    }
}
