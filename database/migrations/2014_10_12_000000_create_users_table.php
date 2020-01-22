<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->index()->nullable();

            $table->string('name', 150);
            $table->string('description')->nullable();

            $table->softDeletes();

            $table->timestamps();
        });  

        Schema::create('role_categories', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('description');

            $table->timestamps();
        });

        Schema::create('roles', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('role_category_id')->unsigned()->index();

            $table->string('name', 150);
            $table->string('description')->nullable();

            $table->timestamps();
        });      

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('department_employee_id')->unsigned()->index()->nullable();

            $table->integer('employee_category_id')->unsigned()->index();
            $table->integer('supervisor_id')->unsigned()->index()->nullable();
            $table->integer('location_id')->unsigned()->index();            

            $table->string('first_name', 255);
            $table->string('middle_name', 255)->nullable();
            $table->string('last_name', 255);
            $table->string('suffix', 10)->nullable();

            $table->string('profile_photo')->nullable();

            $table->string('contact_no', 255)->nullable();
            $table->string('company_no', 255)->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();

            $table->string('job_level')->nullable();
            $table->string('job_grade')->nullable();
            $table->string('cost_center')->nullable();

            $table->string('email')->unique();
            $table->string('password')->nullable();

            $table->string('google_id')->unique()->nullable();
            $table->string('google_name')->nullable();            

            $table->boolean('onVacation')->default(0);
            $table->integer('vacation_proxy_id')->unsigned()->index()->nullable();
            $table->date('vacation_start_date')->nullable();
            $table->date('vacation_end_date')->nullable();

            $table->boolean('notification')->default(1);

            $table->string('verify_token')->unique()->nullable();

           /*
            | Status
            |------------------------------
            | Not verified = 0
            | Enable = 1
            | Disable = 2
            |
            */
            $table->boolean('status')->default(0);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('group_user', function(Blueprint $table) {

            $table->integer('group_id')->unsigned()->index();
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['group_id', 'user_id']);
        });   

        Schema::create('group_role', function(Blueprint $table) {

            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->integer('group_id')->unsigned()->index();
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');    

            $table->primary(['role_id', 'group_id']);
        });

        Schema::create('assignee_user', function(Blueprint $table) {

            $table->bigInteger('assigner_id')->unsigned()->index();
            $table->foreign('assigner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');         

            $table->primary(['assigner_id', 'user_id']);
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        Schema::dropIfExists('assignee_user');
        Schema::dropIfExists('group_role');
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('role_categories');

        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('groups');
    }
}
