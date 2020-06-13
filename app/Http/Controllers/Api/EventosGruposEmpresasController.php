<?php

namespace App\Http\Controllers\Api;

use App\EventoGrupoEmpresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventosGruposEmpresasController extends ApiController
{
    public function getEventosGruposEmpresas($grupo_empresa)
    {
        $data = [];

        $eventos_grupos_empresas = DB::table("eventos_grupos_empresas")->where('grupo_empresa', $grupo_empresa)->get();

        $data['eventos_grupos_empresas'] = $eventos_grupos_empresas;

        return $this->sendResponse($data, "Eventos en grupos empresas recuperados correctamente");
    }

    public function addEventosGruposEmpresas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_empresa' => 'required',
            'creador' => 'required',
            'title' => 'required',
            'descripcion' => 'required',
            'start' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $eventos_grupos_empresas = new EventoGrupoEmpresa();

        $eventos_grupos_empresas->grupo_empresa = $request->get("grupo_empresa");
        $eventos_grupos_empresas->creador = $request->get("creador");
        $eventos_grupos_empresas->title = $request->get("title");
        $eventos_grupos_empresas->descripcion = $request->get("descripcion");
        $eventos_grupos_empresas->start = $request->get("start");
        $eventos_grupos_empresas->save();

        $data = [
            'eventos_grupos_empresas' => $eventos_grupos_empresas
        ];
        return $this->sendResponse($data, "Evento en grupo empresa creado correctamente");
    }
}
