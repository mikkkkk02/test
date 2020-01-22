<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_logs', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_id')->index()->unsigned();
            $table->integer('employee_id')->index()->unsigned();

            $table->text('text');

            $table->text('link')->nullable();

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
        Schema::dropIfExists('form_logs');        
    }
}
