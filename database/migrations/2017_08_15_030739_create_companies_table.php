<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('admin_technician_id')->nullable();
            $table->integer('hr_technician_id')->nullable();
            $table->integer('od_technician_id')->nullable();

            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->text('description')->nullable();

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();

            $table->softDeletes();

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
        Schema::dropIfExists('companies');
    }
}
