<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempFormAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_form_answers', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_id')->unsigned()->index();
            $table->integer('form_template_field_id')->unsigned()->index();

            $table->text('value')->nullable();

            $table->string('value_others')->nullable();



            $table->integer('temp_form_id')->unsigned()->index();



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
        Schema::dropIfExists('temp_form_answers');
    }
}
