<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos_comunidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('comunidad')->unsigned();
            $table->foreign('comunidad')->references('id')->on('comunidades')->onDelete('cascade');
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
        Schema::dropIfExists('eventos_comunidades');
    }
}
