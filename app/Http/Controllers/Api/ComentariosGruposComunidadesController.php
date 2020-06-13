<?php

namespace App\Http\Controllers\Api;

use App\ComentarioGrupoComunidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComentariosGruposComunidadesController extends ApiController
{
    public function getComentariosGruposComunidades($idPublication)
    {
        $data = [];

        $num_comentarios = DB::table("comentarios_grupos_comunidades")->where('publicacion_gru_comunidad', $idPublication)->count();
        $comentarios_grupos_comunidades = DB::table("comentarios_grupos_comunidades")->where('publicacion_gru_comunidad', $idPublication)->get();

        $data = [
            'num_comentarios' => $num_comentarios,
            'comentarios_grupos_comunidades' => $comentarios_grupos_comunidades
        ];

        return $this->sendResponse($data, "Comentarios recuperados correctamente");
    }

    public function addComentariosGruposComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_comunidad' => 'required',
            'creador' => 'required',
            'contenido_comentario' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $comentarios_grupos_comunidades = new ComentarioGrupoComunidad();
        $comentarios_grupos_comunidades->publicacion_gru_comunidad= $request->get("publicacion_grupo_comunidad");
        $comentarios_grupos_comunidades->creador = $request->get("creador");
        $comentarios_grupos_comunidades->contenido_comentario = $request->get("contenido_comentario");
        $comentarios_grupos_comunidades->save();

        $data = [
            'comentarios_grupos_comunidades' => $comentarios_grupos_comunidades
        ];
        return $this->sendResponse($data, "Comentario agregado correctamente");
    }
}
