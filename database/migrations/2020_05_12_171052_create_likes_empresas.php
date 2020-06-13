<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('publicacion_empresa')->unsigned();
            $table->foreign('publicacion_empresa')->references('id')->on('publicaciones_empresas')->onDelete('cascade');
            $table->bigInteger('user')->unsigned();
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('likes_empresas');
    }
}
