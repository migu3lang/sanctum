<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadoClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado_clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('estado',['activo','desactivo','retirado'])->default('activo');
            $table->string('cargo')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('admincliente_id');
            $table->timestamps();
            
           $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('admincliente_id')->references('id')->on('adminclientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empleado_clientes');
    }
}
