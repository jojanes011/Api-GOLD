<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMisComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('mis_comunidades', function (Blueprint $table) {
             $table->bigIncrements('id');
             $table->bigInteger('comunidad')->unsigned();
             $table->foreign('comunidad')->references('id')->on('comunidades')->onDelete('cascade');
             $table->bigInteger('empresa')->unsigned();
             $table->foreign('empresa')->references('id')->on('empresas')->onDelete('cascade');
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
         Schema::dropIfExists('mis_comunidades');
     }
}
