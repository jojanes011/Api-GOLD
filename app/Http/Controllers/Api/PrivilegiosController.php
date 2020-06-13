<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Privilegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrivilegiosController extends ApiController
{
    public function getPrivilegios()
    {
        $data = [];

        $privilegios = DB::table("privilegios")->get();

        $data['privilegios'] = $privilegios;

        return $this->sendResponse($data, "Privilegios recuperados correctamente");
    }

    public function getPrivilegiosDetail($id, Request $request)
    {
        $privilegios = Privilegio::find($id);
        if ($privilegios === null) {
            return $this->sendError("Error en los datos", ["El privilegio no existe"], 422);
        }

        $data = [];
        $data["privilegios"] = $privilegios->find($id);

        return $this->sendResponse($data, "Datos de privilegios recuperados correctamente");
    }

    public function addPrivilegios(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:privilegios',
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $privilegios = new Privilegio();
        $privilegios->nombre = $request->get("nombre");
        $privilegios->save();

        $data = [
            'privilegios' => $privilegios
        ];
        return $this->sendResponse($data, "Privilegio creado correctamente");

    }

    public function updatePrivilegios(Request $request){
        $privilegios = Privilegio::find($request->get("id"));
        if($privilegios === null){
            return $this->sendError("Error en los datos", ["El privilegio no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:privilegios',
        ]);
        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $privilegios->nombre = $request->get("nombre");
        $privilegios->update();

        $data = [
            'privilegios' => $privilegios
        ];
        return $this->sendResponse($data, "Privilegio modificado correctamente");
    }

    public function deletePrivilegios(Request $request)
    {
        $privilegios = Privilegio::find($request->get("id"));
        if ($privilegios === null) {
            return $this->sendError("Error en los datos", ["El privilegio no existe"], 422);
        }
        $privilegios->delete();
        $data = [
            '$privilegios' => $privilegios
        ];
        return $this->sendResponse($data, "Privilegio eliminado correctamente");
    }
}
