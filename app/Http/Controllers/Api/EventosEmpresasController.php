<?php

namespace App\Http\Controllers\Api;

use App\EventoEmpresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventosEmpresasController extends ApiController
{
    public function getEventosEmpresas($empresa)
    {
        $data = [];

        $eventos_empresas = DB::table("eventos_empresas")->where('empresa', $empresa)->get();

        $data['eventos_empresas'] = $eventos_empresas;

        return $this->sendResponse($data, "Eventos en empresas recuperados correctamente");
    }

    public function getEventosEmpresasDetail($id)
    {
        $evento = EventoEmpresa::find($id);
        if ($evento === null) {
            return $this->sendError("Error en los datos", ["El evento en comunidad no existe"], 422);
        }

        $data['evento'] = $evento;

        return $this->sendResponse($data, "Eventos en empresas recuperados correctamente");
    }

    public function addEventosEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'creador' => 'required',
            'title' => 'required',
            'descripcion' => 'required',
            'start' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $eventos_empresas = new EventoEmpresa();

        $eventos_empresas->empresa = $request->get("empresa");
        $eventos_empresas->creador = $request->get("creador");
        $eventos_empresas->title = $request->get("title");
        $eventos_empresas->descripcion = $request->get("descripcion");
        $eventos_empresas->start = $request->get("start");
        $eventos_empresas->end = $request->get("end");
        $eventos_empresas->save();

        $data = [
            'eventos_empresas' => $eventos_empresas
        ];
        return $this->sendResponse($data, "Evento en empresa creado correctamente");

    }
}
