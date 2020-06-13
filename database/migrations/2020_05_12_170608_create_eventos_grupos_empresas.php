<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosGruposEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos_grupos_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('grupo_empresa')->unsigned();
            $table->foreign('grupo_empresa')->references('id')->on('grupos_empresas')->onDelete('cascade');
            $table->bigInteger('creador')->unsigned();
            $table->foreign('creador')->references('id')->on('users')->onDelete('cascade');
            $table->string('title',256);
            $table->longText('descripcion')->nullable();
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->string('color',10);
            $table->string('text_color',10);
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
        Schema::dropIfExists('eventos_grupos_empresas');
    }
}
