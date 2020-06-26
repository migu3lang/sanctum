<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminclientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminclientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombreAdmincliente');
            $table->unsignedInteger('user_id');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('nit')->nullable();
            $table->string('logo')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
           
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adminclientes');
    }
}
