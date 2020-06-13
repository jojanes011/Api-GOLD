<?php

namespace App\Http\Controllers\Api;

use App\ComentarioGrupoEmpresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComentariosGruposEmpresasController extends ApiController
{
    public function getComentariosGruposEmpresas($idPublication)
    {
        $data = [];

        $num_comentarios = DB::table("comentarios_grupos_empresas")->where('publicacion_grupo_empresa', $idPublication)->count();
        $comentarios_grupos_empresas = DB::table("comentarios_grupos_empresas")->where('publicacion_grupo_empresa', $idPublication)->get();

        $data = [
            'num_comentarios' => $num_comentarios,
            'comentarios_grupos_empresas' => $comentarios_grupos_empresas
        ];

        return $this->sendResponse($data, "Comentarios recuperados correctamente");
    }

    public function addComentariosGruposEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_empresa' => 'required',
            'creador' => 'required',
            'contenido_comentario' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $comentarios_grupos_empresas = new ComentarioGrupoEmpresa();
        $comentarios_grupos_empresas->publicacion_grupo_empresa= $request->get("publicacion_grupo_empresa");
        $comentarios_grupos_empresas->creador = $request->get("creador");
        $comentarios_grupos_empresas->contenido_comentario = $request->get("contenido_comentario");
        $comentarios_grupos_empresas->save();

        $data = [
            'comentarios_grupos_empresas' => $comentarios_grupos_empresas
        ];
        return $this->sendResponse($data, "Comentario agregado correctamente");
    }
}
