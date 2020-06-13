<?php

namespace App\Http\Controllers\Api;

use App\GrupoEmpresa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GruposEmpresasController extends ApiController
{
    public function getGruposEmpresas()
    {
        $data = [];
        $grupos_empresas = DB::table("grupos_empresas")->get();
        $data['grupos_empresas'] = $grupos_empresas;

        return $this->sendResponse($data, "Grupos en empresas recuperadas correctamente");
    }

    public function getGruposEmpresasDetail($id, Request $request)
    {
        $grupos_empresas = GrupoEmpresa::find($id);
        if ($grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["Los grupos en empresas no existe"], 422);
        }

        $data = [];
        $data["grupos_empresas"] = $grupos_empresas->find($id);

        return $this->sendResponse($data, "Datos de grupos en empresas recuperados correctamente");
    }

    public function addGruposEmpresas(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:grupos_empresas',
            'descripcion' => 'required',
            'categoria' => 'required',
            'empresa' => 'required',
            'creador' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }
        // Subir fotos
        if (!$request->hasFile('photo')) {
            $fileName = NULL;
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
        $grupos_empresas = new GrupoEmpresa();
        $grupos_empresas->nombre = $request->get("nombre");
        $grupos_empresas->foto = $fileName;
        $grupos_empresas->descripcion = $request->get("descripcion");
        $grupos_empresas->categoria = $request->get("categoria");
        $grupos_empresas->empresa = $request->get("empresa");
        $grupos_empresas->creador = $request->get("creador");
        $grupos_empresas->save();

        $data = [
            'grupos_empresas' => $grupos_empresas
        ];
        return $this->sendResponse($data, "Grupo en empresa creada correctamente");

    }

    public function updateGruposEmpresas(Request $request){
        $grupos_empresas = GrupoEmpresa::find($request->get("id"));
        if($grupos_empresas === null){
            return $this->sendError("Error en los datos", ["El grupo en empresas no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:grupos_empresas',
            'foto' => 'required',
            'descripcion' => 'required',
            'categoria' => 'required',
            'empresa' => 'required',
            'creador' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $grupos_empresas->nombre = $request->get("nombre");
        $grupos_empresas->foto = $request->get("foto");
        $grupos_empresas->descripcion = $request->get("descripcion");
        $grupos_empresas->categoria = $request->get("categoria");
        $grupos_empresas->empresa = $request->get("empresa");
        $grupos_empresas->creador = $request->get("creador");
        $grupos_empresas->update();

        $data = [
            'grupos_empresas' => $grupos_empresas
        ];
        return $this->sendResponse($data, "Grupo en empresa modificada correctamente");
    }

    public function deleteGruposEmpresas(Request $request)
    {
        $grupos_empresas = GrupoEmpresa::find($request->get("id"));
        if ($grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }
        $grupos_empresas->delete();
        $data = [
            '$grupos_empresas' => $grupos_empresas
        ];
        return $this->sendResponse($data, "Grupo en empresa eliminada correctamente");
    }

    public function getCreadorEmpresas($id, Request $request)
    {
        $grupos_empresas = DB::table('grupos_empresas')->where('creador', $id)->get();
        $data = [];
        $data["grupos_empresas"] = $grupos_empresas;

        return $this->sendResponse($data, "Datos de grupos en empresas recuperados correctamente");
    }
}
