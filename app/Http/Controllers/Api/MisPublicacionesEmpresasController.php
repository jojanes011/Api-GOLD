<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MiPublicacionEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisPublicacionesEmpresasController extends ApiController
{
    public function getMisPublicacionesEmpresas($idUser, $idPublication)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_empresas')->where([['user', $idUser],['publicacion_empresa', $idPublication]])->exists();
        $data = [
            'miPublicacion' => $miPublicacion
        ];

        return $this->sendResponse($data, "Likes recuperados correctamente");
    }

    public function getAllPublicacionesEmpresas($user)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_empresas')->where('user', $user)->get();
        $array = [];
        foreach ($miPublicacion as $key) {
            array_push($array, $key->publicacion_empresa);
        }

        $publicaciones = DB::table('publicaciones_empresas')
            ->whereIn('publicaciones_empresas.id', $array)
            ->join('users', 'users.id', 'publicaciones_empresas.user')
            ->join('empresas', 'empresas.id', 'publicaciones_empresas.empresa')
            ->select('users.user', 'publicaciones_empresas.title', 'publicaciones_empresas.id', 'empresas.nombre')
            ->get();

        $data = [
            'publicaciones' => $publicaciones
        ];

        return $this->sendResponse($data, "Publicaciones recuperadas correctamente");
    }

    public function addMisPublicacionesEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_empresa' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $miPublicacion = new MiPublicacionEmpresa();
        $miPublicacion->publicacion_empresa = $request->get("publicacion_empresa");
        $miPublicacion->user = $request->get("user");
        $miPublicacion->save();

        $data = [
            'miPublicacion' => $miPublicacion
        ];
        return $this->sendResponse($data, "Publicación guardada correctamente");
    }

    public function deleteMisPublicacionesEmpresas(Request $request)
    {
        $miPublicacion = MiPublicacionEmpresa::where([["user","=",$request->get("user")],["publicacion_empresa", "=", $request->get("publicacion_empresa")]]);
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
