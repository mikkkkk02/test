<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormHistoryAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_history_answers', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_history_id')->unsigned()->index();

            $table->integer('form_id')->unsigned()->index();
            $table->integer('form_template_field_id')->unsigned()->index();

            $table->text('value')->nullable();

            $table->string('value_others')->nullable();

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
        Schema::dropIfExists('form_history_answers');
    }
}
