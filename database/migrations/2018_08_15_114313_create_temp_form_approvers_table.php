<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempFormApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_form_approvers', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_id')->unsigned()->index();
            $table->integer('form_template_approver_id')->nullable()->unsigned()->index();
            $table->integer('approver_id')->unsigned()->index();

            $table->integer('sort')->default(0);
            $table->text('reason')->nullable();
            
           /*
            | Type
            |------------------------------
            | Level = 1
            | Employee = 2
            | Company = 3
            | CEO = 4
            |
            */ 
            $table->integer('type')->default(1);

            $table->integer('enabled')->default(0);
            $table->integer('status')->default(0);

            $table->datetime('approved_date')->nullable();

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
        Schema::dropIfExists('temp_form_approvers');
    }
}
