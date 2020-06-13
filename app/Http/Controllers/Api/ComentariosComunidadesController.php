<?php

namespace App\Http\Controllers\Api;

use App\ComentarioComunidad;
use App\Http\Controllers\Controller;
use App\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComentariosComunidadesController extends ApiController
{
    public function getComentariosComunidades($idPublication)
    {
        $data = [];

        $num_comentarios = DB::table("comentarios_comunidades")->where('publicacion_comunidad', $idPublication)->count();
        $comentarios_comunidades = DB::table("comentarios_comunidades")
            ->where('publicacion_comunidad', $idPublication)
            ->join('userdata','userdata.iduser','comentarios_comunidades.creador')
            ->select('comentarios_comunidades.contenido_comentario', 'comentarios_comunidades.id', 'userdata.nombre', 'userdata.primer_apellido', 'userdata.foto', 'userdata.iduser')
            ->get();

        $data = [
            'num_comentarios' => $num_comentarios,
            'comentarios_comunidades' => $comentarios_comunidades
        ];

        return $this->sendResponse($data, "Comentarios recuperados correctamente");
    }

    public function addComentariosComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_comunidad' => 'required',
            'creador' => 'required',
            'contenido_comentario' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $comentarios_comunidades = new ComentarioComunidad();
        $comentarios_comunidades->publicacion_comunidad = $request->get("publicacion_comunidad");
        $comentarios_comunidades->creador = $request->get("creador");
        $comentarios_comunidades->contenido_comentario = $request->get("contenido_comentario");
        $comentarios_comunidades->save();

        $data = [
            'comentarios_comunidades' => $comentarios_comunidades
        ];
        return $this->sendResponse($data, "Comentario agregado correctamente");
    }
}
