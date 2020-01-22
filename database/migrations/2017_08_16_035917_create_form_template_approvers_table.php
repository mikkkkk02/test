<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplateApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_template_approvers', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('employee_id')->nullable()->unsigned()->index();
            
           /*
            | Type
            |------------------------------
            | Level = 1
            | Employee = 2
            | Company = 3
            | CEO = 4
            |
            */ 
            $table->integer('type')->default(1);

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
        Schema::dropIfExists('form_template_approvers');
    }
}
