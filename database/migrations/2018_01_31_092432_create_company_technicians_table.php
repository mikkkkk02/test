<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTechniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_admintechnician', function(Blueprint $table) {
            $this->createTableColumns($table);
        });

        Schema::create('company_hrtechnician', function(Blueprint $table) {
            $this->createTableColumns($table);
        });

        Schema::create('company_odtechnician', function(Blueprint $table) {
            $this->createTableColumns($table);
        });                 
    }

    public function createTableColumns($table) {
        $table->integer('company_id')->unsigned()->index();
        $table->foreign('company_id')
            ->references('id')
            ->on('companies')
            ->onDelete('cascade');

        $table->bigInteger('technician_id')->unsigned()->index();
        $table->foreign('technician_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

        $table->primary(['company_id', 'technician_id']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_admintechnician');
        Schema::dropIfExists('company_hrtechnician');
        Schema::dropIfExists('company_odtechnician');
    }
}
