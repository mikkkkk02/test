<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_template_options', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_template_field_id')->unsigned()->index();

            $table->integer('sort')->default(0);
            $table->string('value');

           /*
            | Type
            |------------------------------
            | Textfield = 0
            | Datefield = 1
            | Number = 2
            | Dropdown = 3
            |
            */            
            $table->integer('type')->default(0);
            $table->string('type_value')->nullable();

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
        Schema::dropIfExists('form_template_options');
    }
}
