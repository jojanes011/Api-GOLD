<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionesComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicaciones_comunidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('comunidad')->unsigned();
            $table->foreign('comunidad')->references('id')->on('comunidades')->onDelete('cascade');
            $table->bigInteger('user')->unsigned();
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
            $table->string('title',256);
            $table->longText('contenido')->nullable();
            $table->string('foto',256)->nullable();
            $table->integer('num_likes');
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
        Schema::dropIfExists('publicaciones_comunidades');
    }
}
