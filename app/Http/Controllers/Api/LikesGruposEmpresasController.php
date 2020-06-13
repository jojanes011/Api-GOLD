<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LikeGrupoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikesGruposEmpresasController extends ApiController
{
    public function getLikesGruposEmpresas($idUser, $idPublication)
    {
        $data = [];

        $likes = DB::table("likes_grupos_empresas")->where('publicacion_grupo_empresa', $idPublication)->count();
        $myLike = DB::table('likes_grupos_empresas')->where([['user', $idUser],['publicacion_grupo_empresa', $idPublication]])->exists();
        $data = [
            'likes' => $likes,
            'mi_like' => $myLike
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function addLikesGruposEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_empresa' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $likes_grupos_empresas = new LikeGrupoEmpresa();
        $likes_grupos_empresas->publicacion_grupo_empresa = $request->get("publicacion_grupo_empresa");
        $likes_grupos_empresas->user = $request->get("user");
        $likes_grupos_empresas->save();

        $data = [
            'likes_grupos_empresas' => $likes_grupos_empresas
        ];
        return $this->sendResponse($data, "Like agregado correctamente");
    }

    public function deleteLikesGruposEmpresas(Request $request)
    {
        $likesGruposEmpresas = LikeGrupoEmpresa::where([["user","=",$request->get("user")],["publicacion_grupo_empresa", "=", $request->get("publicacion_grupo_empresa")]]);
        if ($likesGruposEmpresas === null) {
            return $this->sendError("Error en los datos", ["El like no existe",$request->get("id")], 422);
        }
        $likesGruposEmpresas->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Like eliminado correctamente");
    }
}
