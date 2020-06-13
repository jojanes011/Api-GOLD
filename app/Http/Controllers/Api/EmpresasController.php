<?php

namespace App\Http\Controllers\Api;

use App\Empresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmpresasController extends ApiController
{
    public function getEmpresas()
    {
        $data = [];

        $empresas = DB::table("empresas")->get();

        $data['empresas'] = $empresas;

        return $this->sendResponse($data, "Empresas recuperadas correctamente");
    }

    public function getEmpresasDetail($id, Request $request)
    {
        $empresas = Empresa::find($id);
        if ($empresas === null) {
            return $this->sendError("Error en los datos", ["La empresa no existe"], 422);
        }

        $data = [];
        $data["empresas"] = $empresas->find($id);

        return $this->sendResponse($data, "Datos de empresa recuperados correctamente");
    }

    public function addEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:empresas',
            'direccion' => 'required',
            'nit' => 'required',
            'telefono' => 'required',
            'representante_legal' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $empresas = new Empresa();

         // Subir fotos
         if (!$request->hasFile('photo')) {
            $fileName = '1234567890 comunidad.png';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $fileName = '';
            for ($i = 0; $i < 20; $i++) {
                $fileName .= $characters[rand(0, $charactersLength - 1)];
            }
            $fileName .= '.jpg';
            $path = $request->file('photo')->move(public_path("/imagenesPerfil/"), $fileName);
        }

        $empresas->nombre = $request->get("nombre");
        $empresas->foto = $fileName;
        $empresas->direccion = $request->get("direccion");
        $empresas->nit = $request->get("nit");
        $empresas->telefono = $request->get("telefono");
        $empresas->representante_legal = $request->get("representante_legal");
        $empresas->save();

        $data = [
            'empresas' => $empresas
        ];
        return $this->sendResponse($data, "Empresa creada correctamente");
    }

    public function updateEmpresas(Request $request){
        $empresas = Empresa::find($request->get("id"));
        if($empresas === null){
            return $this->sendError("Error en los datos", ["La empresa no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:empresas',
            'foto' => 'required',
            'direccion' => 'required',
            'nit' => 'required',
            'telefono' => 'required',
            'representante_legal' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $empresas->nombre = $request->get("nombre");
        $empresas->foto = $request->get("foto");
        $empresas->foto = $request->get("direccion");
        $empresas->foto = $request->get("nit");
        $empresas->foto = $request->get("telefono");
        $empresas->foto = $request->get("representante_legal");
        $empresas->update();

        $data = [
            'empresas' => $empresas
        ];
        return $this->sendResponse($data, "Empresa modificada correctamente");
    }

    public function deleteEmpresas(Request $request)
    {
        $empresas = Empresa::find($request->get("id"));
        if ($empresas === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }
        $empresas->delete();
        $data = [
            '$empresas' => $empresas
        ];
        return $this->sendResponse($data, "Empresa eliminada correctamente");
    }
}
