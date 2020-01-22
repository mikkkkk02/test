<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_template_fields', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('form_template_id')->unsigned()->index();

            $table->integer('sort')->default(0);
            $table->text('label');
            
           /*
            | Type
            |------------------------------
            | Textfield = 0
            | Textarea = 1
            | Datefield = 2
            | Radiobox = 3
            | Checkbox = 4
            | Table = 5
            | Dropdown = 6
            | Header = 7
            | Paragraph = 8
            |
            */
            $table->integer('type')->default(0);
            $table->integer('type_value')->nullable();

            $table->boolean('hasOthers')->default(0);

            $table->boolean('isRequired')->default(0);

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
        Schema::dropIfExists('form_template_fields');
    }
}
