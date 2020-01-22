<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idps', function(Blueprint $table) {

            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();

            $this->createTableColumns($table);
        });

        Schema::create('temp_idps', function(Blueprint $table) {

            $table->integer('idp_id')->unsigned()->index();

            $table->integer('state')->default(0);

            $table->integer('updater_id')->unsigned()->index();
            $table->integer('approver_id')->unsigned()->index()->nullable();

            $table->softDeletes();

            $this->createTableColumns($table);
        });
    }

    public function createTableColumns($table) {

        $table->increments('id');

        $table->integer('employee_id')->unsigned()->index();
        $table->integer('competency_id')->unsigned()->index();        

       /*
        | Learning Activity Type
        |------------------------------
        | Education = 0
        | Experience = 1
        | Exposure = 2
        |
        */
        $table->integer('learning_type')->default(0);

       /*
        | Competency Type
        |------------------------------
        | Technical = 0
        | Values = 1
        | Leardership = 2
        |
        */            
        $table->integer('competency_type')->default(0);

        $table->decimal('required_proficiency_level')->default(1);
        $table->decimal('current_proficiency_level')->default(1);

       /*
        | Type
        |------------------------------
        | None = 0
        | With gap = 1
        | Continuous learning = 2
        | Additional competency = 3
        |
        */
        $table->integer('type')->default(0);

        $table->text('details')->nullable();

        $table->integer('completion_year');

       /*
        | Status
        |------------------------------
        | On-going = 0
        | For completion = 1
        | Completed = 2
        |
        */            
        $table->integer('status')->default(0);

        $table->timestamps();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idps');
        Schema::dropIfExists('temp_idps');
    }
}
