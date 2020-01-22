<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('department_id')->unsigned()->index()->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();

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
        Schema::dropIfExists('positions');
    }
}
