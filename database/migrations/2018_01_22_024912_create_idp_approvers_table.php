<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdpApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idp_approvers', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('idp_id')->unsigned()->index();
            $table->integer('approver_id')->unsigned()->index();

            $table->integer('sort')->default(0);
            $table->text('reason')->nullable();
            
           /*
            | Type
            |------------------------------
            | Immediate Leader = 0
            | Next Level Leader = 1
            | Group Head = 3
            | OD = 4
            |
            */ 
            $table->integer('type')->default(0);

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
        Schema::dropIfExists('idp_approvers');
    }
}
