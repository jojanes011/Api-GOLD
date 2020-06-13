<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MiPublicacionGrupoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisPublicacionesGruposEmpresasController extends ApiController
{
    public function getMisPublicacionesGruposEmpresas($idUser, $idPublication)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_grupos_empresas')->where([['user', $idUser],['publi_gru_empresa', $idPublication]])->exists();
        $data = [
            'miPublicacion' => $miPublicacion
        ];

        return $this->sendResponse($data, "Mis publicaciones recuperadas correctamente");
    }

    public function getAllPublicacionesGruposEmpresas($user)
    {
        $data = [];
        $miPublicacion = DB::table('mis_publicaciones_grupos_empresas')->where('user', $user)->get();
        $array = [];
        foreach ($miPublicacion as $key) {
            array_push($array, $key->publicacion_grupo_empresa);
        }

        $publicaciones = DB::table('publicaciones_grupos_empresas')
            ->whereIn('publicaciones_grupos_empresas.id', $array)
            ->join('users', 'users.id', 'publicaciones_grupos_empresas.user')
            ->join('grupos_empresas', 'grupos_empresas.id', 'publicaciones_grupos_empresas.grupo_empresa')
            ->select('users.user', 'publicaciones_grupos_empresas.title', 'publicaciones_grupos_empresas.id', 'grupos_empresas.nombre')
            ->get();

        $data = [
            'publicaciones' => $publicaciones
        ];

        return $this->sendResponse($data, "Publicaciones recuperadas correctamente");
    }

    public function addMisPublicacionesGruposEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'publicacion_grupo_empresa' => 'required',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $miPublicacion = new MiPublicacionGrupoEmpresa();
        $miPublicacion->publi_gru_empresa = $request->get("publicacion_grupo_empresa");
        $miPublicacion->user = $request->get("user");
        $miPublicacion->save();

        $data = [
            'miPublicacion' => $miPublicacion
        ];
        return $this->sendResponse($data, "Publicación guardada correctamente");
    }

    public function deleteMisPublicacionesGruposEmpresas(Request $request)
    {
        $miPublicacion = MiPublicacionGrupoEmpresa::where([["user","=",$request->get("user")],["publi_gru_empresa", "=", $request->get("publi_gru_empresa")]]);
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
