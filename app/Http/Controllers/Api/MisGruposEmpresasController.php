<?php

namespace App\Http\Controllers\Api;

use App\GrupoEmpresa;
use App\Http\Controllers\Controller;
use App\MiGrupoEmpresa;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisGruposEmpresasController extends ApiController
{
    public function getMisGruposEmpresas($user)
    {
        $data = [];
        $mis_grupos_empresas = DB::table("mis_grupos_empresas")
        ->where('user',$user)
        ->join('grupos_empresas', 'mis_grupos_empresas.grupo_empresa', 'grupos_empresas.id')
        ->select('grupos_empresas.id','grupos_empresas.nombre')
        ->get();
        $data['mis_grupos_empresas'] = $mis_grupos_empresas;

        return $this->sendResponse($data, "Mis grupos en empresas recuperadas correctamente");
    }

    public function getMisGruposEmpresasDetail($id, Request $request)
    {
        $mis_grupos_empresas = MiGrupoEmpresa::find($id);
        if ($mis_grupos_empresas === null) {
            return $this->sendError("No existe el dato en mi empresa", ["No existe"], 422);
        }

        $grupo_empresa = GrupoEmpresa::find($mis_grupos_empresas->grupo_empresa);
        if ($grupo_empresa === null) {
            return $this->sendError("Error en los datos", ["El grupo empresa no existe"], 422);
        }

        $user = User::find($mis_grupos_empresas->user);
        if ($user === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $mi_grupo_empresa = DB::table("mis_grupos_empresas")
            ->where("mis_grupos_empresas.grupo_empresa", "=", $mis_grupos_empresas->grupo_empresa)
            ->where("mis_grupos_empresas.user", "=", $mis_grupos_empresas->user)
            ->join("grupo_empresa", "mis_empresas.id", "grupos_empresas.id")
            ->join("users", "mis_empresas.id", "users.id")
            ->select("users.id as id_user", "users.user", "users.privilege", "grupos_empresas.id as id_grupo_empresa", "grupos_empresas.foto as foto_grupo_empresa", "grupos_empresas.nombre", "grupos_empresas.descripcion", "grupos_empresas.creador", "grupos_empresas.empresa", "grupos_empresas.created_at")
            ->get();

        $data = [
            'grupo_empresa' => $grupo_empresa,
            'user' => $user,
            'mi_grupo_empresa' => $mi_grupo_empresa
        ];
        return $this->sendResponse($data, "Mi grupo empresa recuperada correctamente");
    }

    public function addMisGruposEmpresas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'grupo_empresa' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_grupos_empresas = new MiGrupoEmpresa();
        $mis_grupos_empresas->grupo_empresa = $request->get("grupo_empresa");
        $mis_grupos_empresas->user = $request->get("user");
        $mis_grupos_empresas->save();

        $data = [
            'mis_empresas' => $mis_grupos_empresas
        ];
        return $this->sendResponse($data, "Mi grupo empresa vinculado correctamente");
    }

    public function updateMisGruposEmpresas(Request $request)
    {
        $mis_grupos_empresas = MiGrupoEmpresa::find($request->get("id"));
        if ($mis_grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'grupo_empresa' => 'required',
            'user' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_grupos_empresas->grupo_empresa = $request->get("grupo_empresa");
        $mis_grupos_empresas->user = $request->get("user");
        $mis_grupos_empresas->update();

        $data = [
            'mis_grupos_empresas' => $mis_grupos_empresas
        ];
        return $this->sendResponse($data, "Mi grupo empresa modificado correctamente");
    }

    public function deleteMisGruposEmpresas(Request $request)
    {
        $mis_grupos_empresas = MiGrupoEmpresa::where([['grupo_empresa',"=",$request->get("grupo")],['user',"=",$request->get("id")]]);
        if ($mis_grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }
        $mis_grupos_empresas->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Mi grupo empresa eliminado correctamente");
    }

    public function getMiembrosGruposEmpresas($id, Request $request)
    {
        $grupo_empresa = GrupoEmpresa::find($id);
        if ($grupo_empresa === null) {
            return $this->sendError("Error en los datos", ["El grupo empresa no existe"], 422);
        }

        $mi_grupo_empresa = DB::table("mis_grupos_empresas")
            ->where("mis_grupos_empresas.grupo_empresa", "=", $id)
            ->join("users", "mis_grupos_empresas.user", "users.id")
            ->join("grupos_empresas", "mis_grupos_empresas.grupo_empresa", "grupos_empresas.id")
            ->select("users.id as id_user", "users.user")
            ->get();

        $data = [
            'mis_grupos_empresas' => $mi_grupo_empresa
        ];
        return $this->sendResponse($data, "Miembros recuperados correctamente");
    }
}
