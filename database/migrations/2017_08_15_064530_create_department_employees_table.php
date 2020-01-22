<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_employees', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('employee_id')->unsigned()->index();
            $table->integer('department_id')->unsigned()->index();

            $table->integer('position_id')->unsigned()->index()->nullable();
            $table->integer('team_id')->unsigned()->index()->nullable();

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
        Schema::dropIfExists('department_employees');
    }
}
