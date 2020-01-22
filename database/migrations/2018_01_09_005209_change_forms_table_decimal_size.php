<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFormsTableDecimalSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function(Blueprint $table) {
            $table->decimal('course_cost', 19, 4)->change();
            $table->decimal('accommodation_cost', 19, 4)->change();
            $table->decimal('meal_cost', 19, 4)->change();
            $table->decimal('transport_cost', 19, 4)->change();
            $table->decimal('others_cost', 19, 4)->change();
            $table->decimal('total_cost', 19, 4)->change();            
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
