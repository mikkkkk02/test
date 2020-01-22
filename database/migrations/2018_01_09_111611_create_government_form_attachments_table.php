<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGovernmentFormAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('government_form_attachments', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('government_form_id')->unsigned()->integer();

            $table->string('name')->nullable();

            $table->string('path');

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
        Schema::dropIfExists('government_form_attachments');
    }
}
