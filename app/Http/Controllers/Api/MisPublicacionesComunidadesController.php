<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MiPublicacionComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisPublicacionesComunidadesController extends ApiController
{
    public function getMisPublicacionesComunidades($idUser, $idPublication)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_comunidades')->where([['user', $idUser],['publicacion_comunidad', $idPublication]])->exists();
        $data = [
            'miPublicacion' => $miPublicacion
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function getAllPublicacionesComunidades($user)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_comunidades')->where('user', $user)->get();
        $array = [];
        foreach ($miPublicacion as $key) {
            array_push($array, $key->publicacion_comunidad);
        }

        $publicaciones = DB::table('publicaciones_comunidades')
            ->whereIn('publicaciones_comunidades.id', $array)
            ->join('users', 'users.id', 'publicaciones_comunidades.user')
            ->join('comunidades', 'comunidades.id', 'publicaciones_comunidades.comunidad')
            ->select('users.user', 'publicaciones_comunidades.title', 'publicaciones_comunidades.id', 'comunidades.nombre')
            ->get();

        $data = [
            'publicaciones' => $publicaciones
        ];

        return $this->sendResponse($data, "Publicaciones recuperadas correctamente");
    }

    public function addMisPublicacionesComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_comunidad' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $miPublicacion = new MiPublicacionComunidad();
        $miPublicacion->publicacion_comunidad = $request->get("publicacion_comunidad");
        $miPublicacion->user = $request->get("user");
        $miPublicacion->save();

        $data = [
            'miPublicacion' => $miPublicacion
        ];
        return $this->sendResponse($data, "Publicación guardada correctamente");
    }

    public function deleteMisPublicacionesComunidades(Request $request)
    {
        $miPublicacion = MiPublicacionComunidad::where([["user","=",$request->get("user")],["publicacion_comunidad", "=", $request->get("publicacion_comunidad")]]);
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
