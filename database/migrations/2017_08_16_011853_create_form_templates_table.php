<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_templates', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('form_template_category_id')->unsigned()->index();

            $table->string('name');
            $table->text('description')->nullable();

            $table->integer('sla')->default(3);

           /*
            | SLA Option
            |------------------------------
            | Start upon approval of the form = 0
            | Base on any date field on the form = 1
            |
            */
            $table->integer('sla_option')->default(0);
            $table->integer('sla_date_id')->nullable();

           /*
            | Approval Option
            |------------------------------
            | In order = 0
            | Simultaneously = 1
            |
            */            
            $table->integer('approval_option')->default(0);

           /*
            | Type
            |------------------------------
            | Admin = 0
            | HR = 1
            | OD = 2
            |
            */            
            $table->integer('type')->default(0);

           /*
            | Priority
            |------------------------------
            | Low = 0
            | Medium = 1
            | High = 2
            |
            */     
            $table->integer('priority')->default(0);

            $table->boolean('enableAttachment')->default(0);
            $table->boolean('isEnable')->default(0);

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
        Schema::dropIfExists('form_templates');
    }
}
