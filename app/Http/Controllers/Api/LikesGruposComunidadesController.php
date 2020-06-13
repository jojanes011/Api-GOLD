<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LikeGrupoComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikesGruposComunidadesController extends ApiController
{
    public function getLikesGruposComunidades($idUser, $idPublication)
    {
        $data = [];

        $likes = DB::table("likes_grupos_comunidades")->where('publicacion_grupo_comunidad', $idPublication)->count();
        $myLike = DB::table('likes_grupos_comunidades')->where([['user', $idUser],['publicacion_grupo_comunidad', $idPublication]])->exists();
        $data = [
            'likes' => $likes,
            'mi_like' => $myLike
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function addLikesGruposComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_comunidad' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $likes_grupos_comunidades = new LikeGrupoComunidad();
        $likes_grupos_comunidades->publicacion_grupo_comunidad = $request->get("publicacion_grupo_comunidad");
        $likes_grupos_comunidades->user = $request->get("user");
        $likes_grupos_comunidades->save();

        $data = [
            'likes_grupos_comunidades' => $likes_grupos_comunidades
        ];
        return $this->sendResponse($data, "Like agregado correctamente");
    }

    public function deleteLikesGruposComunidades(Request $request)
    {
        $likesGruposComunidades = LikeGrupoComunidad::where([["user","=",$request->get("user")],["publicacion_grupo_comunidad", "=", $request->get("publicacion_grupo_comunidad")]]);
        if ($likesGruposComunidades === null) {
            return $this->sendError("Error en los datos", ["El like no existe",$request->get("id")], 422);
        }
        $likesGruposComunidades->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Like eliminado correctamente");
    }
}
