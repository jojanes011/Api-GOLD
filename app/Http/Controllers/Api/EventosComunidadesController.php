<?php

namespace App\Http\Controllers\Api;

use App\EventoComunidad;
use App\PublicacionComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventosComunidadesController extends ApiController
{
    public function getEventosComunidades($comunidad)
    {
        $data = [];

        $eventos_comunidades = DB::table("eventos_comunidades")->where('comunidad', $comunidad)->get();

        $data['eventos_comunidades'] = $eventos_comunidades;

        return $this->sendResponse($data, "Eventos en comunidades recuperados correctamente");
    }

    public function getEventosComunidadesDetail($id)
    {
        $evento = EventoComunidad::find($id);
        if ($evento === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }

        $data['evento'] = $evento;

        return $this->sendResponse($data, "Eventos en comunidades recuperados correctamente");
    }

    public function addEventosComunidades(Request $request){
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required',
            'creador' => 'required',
            'title' => 'required',
            'descripcion' => 'required',
            'start' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $eventos_comunidades = new EventoComunidad();

        $eventos_comunidades->comunidad = $request->get("comunidad");
        $eventos_comunidades->creador = $request->get("creador");
        $eventos_comunidades->title = $request->get("title");
        $eventos_comunidades->descripcion = $request->get("descripcion");
        $eventos_comunidades->start = $request->get("start");
        $eventos_comunidades->end = $request->get("end");
        $eventos_comunidades->save();

        $data = [
            'eventos_comunidades' => $eventos_comunidades
        ];
        return $this->sendResponse($data, "Evento en comunidad creado correctamente");

    }
}
