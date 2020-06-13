<?php

namespace App\Http\Controllers\Api;

use App\Comunidad;
use App\Empresa;
use App\MiComunidad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class MisComunidadesController extends ApiController
{
    public function getMisComunidades($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            return $this->sendError("No existe el usuario", ["No existe"], 422);
        }
        $empresas =  DB::table('mis_empresas')
            ->select('empresa')
            ->where([['user', $id],['aceptado',1]])
            ->orderBy('empresa', 'asc')->get();
        $array = [];
        foreach ($empresas as $value) {
            array_push($array, $value->empresa);
        }

        $mis_comunidades = DB::table('mis_comunidades')
        ->whereIn('mis_comunidades.empresa', $array)
        ->join("comunidades", "mis_comunidades.id", "comunidades.id")
        ->select("mis_comunidades.comunidad","comunidades.foto", "comunidades.nombre")
        // ->select("comunidad")
        ->orderBy('comunidad', 'asc')
        ->get();

        // $datos = [];
        // $ind = 0;
        // foreach ($mis_comunidades as $value) {
        //     $datos[$ind] = DB::table('comunidades')->where('id', $value->comunidad)->get();
        //     $ind++;
        // }

        $data = [
            'empresas' => $array,
            'mis_comunidades' => $mis_comunidades,
            // 'comunidades' => $datos
        ];

        return $this->sendResponse($data, "Mis comunidades recuperadas correctamente");
    }

    public function getMisComunidadesDetail($id, Request $request)
    {
        $mis_comunidades = MiComunidad::find($id);
        if ($mis_comunidades === null) {
            return $this->sendError("No existe el dato en mi comunidad", ["No existe"], 422);
        }

        $comunidad = Comunidad::find($mis_comunidades->comunidad);
        if ($comunidad === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $empresa = Empresa::find($mis_comunidades->empresa);
        if ($empresa === null) {
            return $this->sendError("Error en los datos", ["La empresa no existe"], 422);
        }

        $mi_comunidad = DB::table("mis_comunidades")
            ->where("mis_comunidades.comunidad", "=", $mis_comunidades->comunidad)
            ->where("mis_comunidades.empresa", "=", $mis_comunidades->empresa)
            ->join("comunidades", "mis_comunidades.id", "comunidades.id")
            ->join("empresas", "mis_comunidades.id", "empresas.id")
            ->select("empresas.id as id_empresa", "empresas.nombre as nombre_empresa", "empresas.foto as foto_empresa", "empresas.descripcion", "empresas.nit", "empresas.telefono", "empresas.representante_legal", "comunidades.id as id_comunidad", "comunidades.foto as foto_comunidad", "comunidades.nombre as nombre_comunidad")
            ->get();

        $data = [
            'comunidad' => $comunidad,
            'empresa' => $empresa,
            'mi_comunidad' => $mi_comunidad
        ];
        return $this->sendResponse($data, "Mi comunidad recuperada correctamente");
    }

    public function addMisComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'comunidad' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_comunidades = new MiComunidad();
        $mis_comunidades->comunidad = $request->get("comunidad");
        $mis_comunidades->empresa = $request->get("empresa");
        $mis_comunidades->save();

        $data = [
            'mis_comunidades' => $mis_comunidades
        ];
        return $this->sendResponse($data, "Mi comunidad vinculada correctamente");
    }

    public function updateMisComunidades(Request $request)
    {
        $mis_comunidades = MiComunidad::find($request->get("id"));
        if ($mis_comunidades === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'comunidad' => 'required',
            'empresa' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_comunidades->comunidad = $request->get("comunidad");
        $mis_comunidades->empresa = $request->get("empresa");
        $mis_comunidades->update();

        $data = [
            'mis_comunidades' => $mis_comunidades
        ];
        return $this->sendResponse($data, "Mi comunidad modificada correctamente");
    }

    public function deleteMisComunidades(Request $request)
    {
        $mis_comunidades = MiComunidad::find($request->get("id"));
        if ($mis_comunidades === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }
        $mis_comunidades->delete();
        $data = [
            '$mis_comunidades' => $mis_comunidades
        ];
        return $this->sendResponse($data, "Mi comunidad eliminada correctamente");
    }

    public function getNoMiembros($id, Request $request)
    {
        $comunidad = Comunidad::find($id);
        if ($comunidad === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $empresas_miembros =  DB::table('mis_comunidades')->select('empresa')->where('comunidad', '=', $id)->orderBy('empresa', 'asc')->get();
        $array = [];
        foreach ($empresas_miembros as $value) {
            array_push($array, $value->empresa);
        }
        $empresas = DB::table('empresas')->whereNotIn('id', $array)->get();

        $data = [
            'comunidad' => $comunidad,
            'empresas' => $empresas
        ];

        return $this->sendResponse($data, "Empresas recuperadas correctamente");
    }

    public function getMiembros($id, Request $request)
    {
        $comunidad = Comunidad::find($id);
        if ($comunidad === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $mi_comunidad = DB::table("mis_comunidades")
            ->where("mis_comunidades.comunidad", "=", $id)
            ->join("comunidades", "mis_comunidades.comunidad", "comunidades.id")
            ->join("empresas", "mis_comunidades.empresa", "empresas.id")
            ->select("empresas.id as id_empresa", "empresas.nombre as nombre_empresa", "empresas.foto as foto_empresa", "empresas.direccion", "empresas.nit", "empresas.telefono", "empresas.representante_legal", "comunidades.id as id_comunidad", "comunidades.foto as foto_comunidad", "comunidades.nombre as nombre_comunidad")
            ->get();

        $data = [
            'mis_comunidades' => $mi_comunidad
        ];
        return $this->sendResponse($data, "Miembros recuperados correctamente");
    }
}
