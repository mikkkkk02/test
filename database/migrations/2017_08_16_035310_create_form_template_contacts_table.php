<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_template_contacts', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('form_template_id')->unsigned()->index();
            $table->integer('employee_id')->unsigned()->index();

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
        Schema::dropIfExists('form_template_contacts');
    }
}
