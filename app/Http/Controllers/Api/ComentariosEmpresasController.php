<?php

namespace App\Http\Controllers\Api;

use App\ComentarioEmpresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComentariosEmpresasController extends ApiController
{
    public function getComentariosEmpresas($idPublication)
    {
        $data = [];

        $num_comentarios = DB::table("comentarios_empresas")->where('publicacion_empresa', $idPublication)->count();
        $comentarios_empresas = DB::table("comentarios_empresas")->where('publicacion_empresa', $idPublication)->get();

        $data = [
            'num_comentarios' => $num_comentarios,
            'comentarios_empresas' => $comentarios_empresas
        ];

        return $this->sendResponse($data, "Comentarios recuperados correctamente");
    }

    public function addComentariosEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_empresa' => 'required',
            'creador' => 'required',
            'contenido_comentario' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $comentarios_empresas = new ComentarioEmpresa();
        $comentarios_empresas->publicacion_empresa = $request->get("publicacion_empresa");
        $comentarios_empresas->creador = $request->get("creador");
        $comentarios_empresas->contenido_comentario = $request->get("contenido_comentario");
        $comentarios_empresas->save();

        $data = [
            'comentarios_empresas' => $comentarios_empresas
        ];
        return $this->sendResponse($data, "Comentario agregado correctamente");
    }
}
