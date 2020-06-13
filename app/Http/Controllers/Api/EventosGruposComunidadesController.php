<?php

namespace App\Http\Controllers\Api;

use App\EventoGrupoComunidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventosGruposComunidadesController extends ApiController
{
    public function getEventosGruposComunidades($grupo_comunidad)
    {
        $data = [];

        $eventos_grupos_comunidades = DB::table("eventos_grupos_comunidades")->where('grupo_comunidad', $grupo_comunidad)->get();

        $data['eventos_grupos_comunidades'] = $eventos_grupos_comunidades;

        return $this->sendResponse($data, "Eventos en grupos comunidades recuperados correctamente");
    }

    public function getEventosGruposComunidadesDetail($id)
    {
        $evento = EventoGrupoComunidad::find($id);
        if ($evento === null) {
            return $this->sendError("Error en los datos", ["El evento en el grupo comunidad no existe"], 422);
        }

        $data['evento'] = $evento;

        return $this->sendResponse($data, "Eventos en grupos comunidades recuperados correctamente");
    }

    public function addEventosGruposComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_comunidad' => 'required',
            'creador' => 'required',
            'title' => 'required',
            'descripcion' => 'required',
            'start' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $eventos_grupos_comunidades = new EventoGrupoComunidad();

        $eventos_grupos_comunidades->grupo_comunidad = $request->get("grupo_comunidad");
        $eventos_grupos_comunidades->creador = $request->get("creador");
        $eventos_grupos_comunidades->title = $request->get("title");
        $eventos_grupos_comunidades->descripcion = $request->get("descripcion");
        $eventos_grupos_comunidades->start = $request->get("start");
        $eventos_grupos_comunidades->end = $request->get("end");
        $eventos_grupos_comunidades->save();

        $data = [
            'eventos_grupos_comunidades' => $eventos_grupos_comunidades
        ];
        return $this->sendResponse($data, "Evento en grupo comunidad creado correctamente");
    }
}
