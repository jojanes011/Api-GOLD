<?php

namespace App\Http\Controllers\Api;

use App\Empresa;
use App\Http\Controllers\Controller;
use App\MiEmpresa;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisEmpresasController extends ApiController
{
    public function getMisEmpresas()
    {
        $data = [];
        $mis_empresas = DB::table("mis_empresas")->get();
        $data['mis_empresas'] = $mis_empresas;

        return $this->sendResponse($data, "Mis empresas recuperadas correctamente");
    }

    public function getMisEmpresasDetail($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $mi_empresa = DB::table("mis_empresas")
            ->where("mis_empresas.user", "=", $id)
            ->join("empresas", "mis_empresas.empresa", "empresas.id")
            ->join("users", "mis_empresas.user", "users.id")
            ->select("empresas.id as id_empresa","empresas.foto as foto", "empresas.nombre as nombre_empresa", "empresas.foto as foto_empresa", "empresas.direccion", "empresas.nit", "empresas.telefono", "empresas.representante_legal", "users.id as id_user", "users.user", "users.privilegio")
            ->get();

        $data = [
            'mis_empresas' => $mi_empresa
        ];
        return $this->sendResponse($data, "Mi empresa recuperada correctamente");
    }

    public function getSolicitudes($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $mi_empresa = DB::table("mis_empresas")
            ->where("user", "=", $id)
            ->get();

        $empresas = [];
        foreach ($mi_empresa as $empresa) {
            array_push($empresas, $empresa->empresa);
        }

        $usuarios = DB::table('mis_empresas')
            ->whereIn('empresa', $empresas)
            ->where('aceptado', 0)
            ->join("empresas", "mis_empresas.empresa", "empresas.id")
            ->join('users', 'mis_empresas.user', 'users.id')
            ->select('mis_empresas.id','empresas.nombre as nombre_empresa', 'users.user')
            ->get();



        $data = [
            'usuarios' => $usuarios
        ];
        return $this->sendResponse($data, "Mi empresa recuperada correctamente");
    }

    public function addMisEmpresas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'user' => 'required',
            'aceptado' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_empresas = new MiEmpresa();
        $mis_empresas->empresa = $request->get("empresa");
        $mis_empresas->user = $request->get("user");
        $mis_empresas->aceptado = $request->get("aceptado");
        $mis_empresas->save();

        $data = [
            'mis_empresas' => $mis_empresas
        ];
        return $this->sendResponse($data, "Mi empresa vinculada correctamente");
    }

    public function updateMisEmpresas(Request $request)
    {
        $mis_empresas = MiEmpresa::find($request->get("id"));
        if ($mis_empresas === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'user' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_empresas->empresa = $request->get("empresa");
        $mis_empresas->user = $request->get("user");
        $mis_empresas->update();

        $data = [
            'mis_empresas' => $mis_empresas
        ];
        return $this->sendResponse($data, "Mi empresa modificada correctamente");
    }

    public function deleteMisEmpresas(Request $request)
    {
        $mis_empresas = MiEmpresa::where([['empresa',"=",$request->get("empresa")],['user',"=",$request->get("id")]]);
        if ($mis_empresas === null) {
            return $this->sendError("Error en los datos", ["El like no existe",$request->get("id")], 422);
        }

        $mis_empresas->delete();
        $data = [
            'status' => 'OK'
        ];
        return $this->sendResponse($data, "Mi empresa eliminada correctamente");
    }

    public function getNoEmpresas($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $empresas_miembros =  DB::table('mis_empresas')->select('empresa')->where('user', '=', $id)->orderBy('user','asc')->get();
        $array = [];
        foreach ($empresas_miembros as $value) {
            array_push($array, $value->empresa);
        }
        $empresas = DB::table('empresas')->whereNotIn('id', $array)->get();

        $data = [
            'user' => $user,
            'empresas' => $empresas
            // 'mimebros' => $empresas_miembros
        ];

        return $this->sendResponse($data, "Empresas recuperadas correctamente");
    }

    public function getMiembros($id)
    {
        $empresa = Empresa::find($id);
        if ($empresa === null) {
            return $this->sendError("Error en los datos", ["La empresa no existe"], 422);
        }
        $mi_empresa = DB::table('mis_empresas')
            ->where("mis_empresas.empresa", "=", $id)
            ->join("empresas", "mis_empresas.empresa", "empresas.id")
            ->join("userdata", "mis_empresas.user", "userdata.iduser")
            ->select("userdata.iduser as id_user", "userdata.nombre", "userdata.primer_apellido", "userdata.segundo_apellido")
            ->get();
            $data = [
                'mis_empresas' => $mi_empresa
            ];
            return $this->sendResponse($data, "Miembros recuperados correctamente");
    }

    public function aceptarSolicitud(Request $request)
    {
        $mis_empresas = MiEmpresa::find($request->get("id"));
        if ($mis_empresas === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $mis_empresas->aceptado = 1;
        $mis_empresas->update();

        $data = [
            'mis_empresas' => $mis_empresas
        ];
        return $this->sendResponse($data, "Mi empresa modificada correctamente");
    }
}
