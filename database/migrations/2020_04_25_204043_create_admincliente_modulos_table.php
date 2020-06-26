<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminclienteModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admincliente_modulos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admincliente_id');
            $table->unsignedInteger('modulo_id');
            
            $table->timestamps();

            $table->foreign('admincliente_id')->references('id')->on('adminclientes');
            $table->foreign('modulo_id')->references('id')->on('modulos');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admincliente_modulos');
    }
}
