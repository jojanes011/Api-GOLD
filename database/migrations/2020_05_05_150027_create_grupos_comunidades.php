<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('grupos_comunidades', function (Blueprint $table) {
             $table->bigIncrements('id');
             $table->string('nombre',256);
             $table->string('descripcion',256);
             $table->string('foto', 256)->nullable();
             $table->bigInteger('creador')->unsigned();
             $table->foreign('creador')->references('id')->on('users')->onDelete('cascade');
             $table->bigInteger('comunidad')->unsigned();
             $table->foreign('comunidad')->references('id')->on('comunidades')->onDelete('cascade');
             $table->timestamps();
             $table->engine = 'InnoDB';
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists('grupos_comunidades');
     }
}
