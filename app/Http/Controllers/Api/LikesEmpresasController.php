<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LikeEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikesEmpresasController extends ApiController
{
    public function getLikesEmpresas($idUser, $idPublication)
    {
        $data = [];

        $likes = DB::table("likes_empresas")->where('publicacion_empresa', $idPublication)->count();
        $myLike = DB::table('likes_empresas')->where([['user', $idUser],['publicacion_empresa', $idPublication]])->exists();
        $data = [
            'likes' => $likes,
            'mi_like' => $myLike
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function addLikesEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_empresa' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $likes_empresas = new LikeEmpresa();
        $likes_empresas->publicacion_empresa = $request->get("publicacion_empresa");
        $likes_empresas->user = $request->get("user");
        $likes_empresas->save();

        $data = [
            'likes_empresas' => $likes_empresas
        ];
        return $this->sendResponse($data, "Like agregado correctamente");
    }

    public function deleteLikesEmpresas(Request $request)
    {
        $likesEmpresas = LikeEmpresa::where([["user","=",$request->get("user")],["publicacion_empresa", "=", $request->get("publicacion_empresa")]]);
        if ($likesEmpresas === null) {
            return $this->sendError("Error en los datos", ["El like no existe",$request->get("id")], 422);
        }
        $likesEmpresas->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Like eliminado correctamente");
    }
}
