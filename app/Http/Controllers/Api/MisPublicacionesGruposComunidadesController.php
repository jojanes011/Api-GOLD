<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MiPublicacionGrupoComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisPublicacionesGruposComunidadesController extends ApiController
{
    public function getMisPublicacionesGruposComunidades($idUser, $idPublication)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_grupos_comunidades')->where([['user', $idUser],['publi_gru_comunidad', $idPublication]])->exists();
        $data = [
            'miPublicacion' => $miPublicacion
        ];

        return $this->sendResponse($data, "Mis publicaciones recuperadas correctamente");
    }

    public function getAllPublicacionesGruposComunidades($user)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_grupos_comunidades')->where('user', $user)->get();
        $array = [];
        foreach ($miPublicacion as $key) {
            array_push($array, $key->publicacion_grupo_comunidad);
        }

        $publicaciones = DB::table('publicaciones_grupos_comunidades')
            ->whereIn('publicaciones_grupos_comunidades.id', $array)
            ->join('users', 'users.id', 'publicaciones_grupos_comunidades.user')
            ->join('grupos_comunidades', 'grupos_comunidades.id', 'publicaciones_grupos_comunidades.grupo_comunidad')
            ->select('users.user', 'publicaciones_grupos_comunidades.title', 'publicaciones_grupos_comunidades.id', 'grupos_comunidades.nombre')
            ->get();

        $data = [
            'publicaciones' => $publicaciones
        ];

        return $this->sendResponse($data, "Publicaciones recuperadas correctamente");
    }

    public function addMisPublicacionesGruposComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_comunidad' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $miPublicacion = new MiPublicacionGrupoComunidad();
        $miPublicacion->publi_gru_comunidad = $request->get("publicacion_grupo_comunidad");
        $miPublicacion->user = $request->get("user");
        $miPublicacion->save();

        $data = [
            'miPublicacion' => $miPublicacion
        ];
        return $this->sendResponse($data, "Publicación guardada correctamente");
    }

    public function deleteMisPublicacionesGruposComunidades(Request $request)
    {
        $miPublicacion = MiPublicacionGrupoComunidad::where([["user","=",$request->get("user")],["publi_gru_comunidad", "=", $request->get("publi_gru_comunidad")]]);
        if ($miPublicacion === null) {
            return $this->sendError("Error en los datos", ["No está guardado",$request->get("id")], 422);
        }
        $miPublicacion->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Eliminado correctamente");
    }
}
