<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LikeComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikesComunidadesController extends ApiController
{
    public function getLikesComunidades($idUser, $idPublication)
    {
        $data = [];

        $likes = DB::table("likes_comunidades")->where('publicacion_comunidad', $idPublication)->count();
        $myLike = DB::table('likes_comunidades')->where([['user', $idUser],['publicacion_comunidad', $idPublication]])->exists();
        $data = [
            'likes' => $likes,
            'mi_like' => $myLike
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function addLikesComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_comunidad' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $likes_comunidades = new LikeComunidad();
        $likes_comunidades->publicacion_comunidad = $request->get("publicacion_comunidad");
        $likes_comunidades->user = $request->get("user");
        $likes_comunidades->save();

        $data = [
            'likes_comunidades' => $likes_comunidades
        ];
        return $this->sendResponse($data, "Like agregado correctamente");
    }

    public function deleteLikesComunidades(Request $request)
    {
        $likesComunidades = LikeComunidad::where([["user","=",$request->get("user")],["publicacion_comunidad", "=", $request->get("publicacion_comunidad")]]);
        if ($likesComunidades === null) {
            return $this->sendError("Error en los datos", ["El like no existe",$request->get("id")], 422);
        }
        $likesComunidades->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Like eliminado correctamente");
    }
}
