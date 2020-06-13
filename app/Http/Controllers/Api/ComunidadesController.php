<?php

namespace App\Http\Controllers\Api;


use App\Comunidad;
use App\Empresa;
use App\MiComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComunidadesController extends ApiController
{
    public function getComunidades()
    {
        $data = [];

        $comunidades = DB::table("comunidades")->get();

        $data['comunidades'] = $comunidades;

        return $this->sendResponse($data, "Comunidad recuperadas correctamente");
    }

    public function getComunidadesDetail($id, Request $request)
    {
        $comunidades = Comunidad::find($id);
        if ($comunidades === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $data = [];
        $data["comunidades"] = $comunidades->find($id);

        return $this->sendResponse($data, "Datos de comunidad recuperados correctamente");
    }

    public function getMisComunidades($id, Request $request)
    {
        $comunidades = Comunidad::find($id);
        if ($comunidades === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $data = [];
        $data["comunidades"] = $comunidades->find($id);

        return $this->sendResponse($data, "Datos de comunidad recuperados correctamente");
    }

    public function addComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:comunidades'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $comunidades = new Comunidad();

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

        $comunidades->nombre = $request->get("nombre");
        $comunidades->foto = $fileName;
        $comunidades->save();

        $data = [
            'comunidades' => $comunidades
        ];
        return $this->sendResponse($data, "Comunidad creada correctamente");
    }

    public function updateComunidades(Request $request)
    {
        $comunidades = Comunidad::find($request->get("id"));
        if ($comunidades === null) {
            return $this->sendError("Error en los datos", ["La comunidad no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:comunidades',
            'foto' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $comunidades->nombre = $request->get("nombre");
        $comunidades->foto = $request->get("foto");
        $comunidades->update();

        $data = [
            'comunidades' => $comunidades
        ];
        return $this->sendResponse($data, "Comunidad modificada correctamente");
    }

    public function deleteComunidades(Request $request)
    {
        $comunidades = Comunidad::find($request->get("id"));
        if ($comunidades === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }
        $comunidades->delete();
        $data = [
            '$comunidades' => $comunidades
        ];
        return $this->sendResponse($data, "Comunidad eliminada correctamente");
    }
}
