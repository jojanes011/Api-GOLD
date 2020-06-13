<?php

namespace App\Http\Controllers\Api;

use App\Clasificado;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClasificadosController extends ApiController
{
    public function getClasificados($comunidad)
    {
        $data = [];

        $clasificados = DB::table("clasificados")->where('comunidad', $comunidad)->get();

        $data['clasificados'] = $clasificados;

        return $this->sendResponse($data, "Clasificados recuperados correctamente");
    }

    public function addClasificados(Request $request){
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required',
            'user' => 'required',
            'title' => 'required',
            'descripcion' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $clasificados = new Clasificado();


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
            $path = $request->file('photo')->move(public_path("/imagenesClasificados/"), $fileName);
        }
        $clasificados->comunidad = $request->get("comunidad");
        $clasificados->user = $request->get("user");
        $clasificados->title = $request->get("title");
        $clasificados->descripcion = $request->get("descripcion");
        $clasificados->foto = $fileName;
        $clasificados->save();

        $data = [
            'clasificados' => $clasificados
        ];
        return $this->sendResponse($data, "Clasificado creado correctamente");

    }
}
