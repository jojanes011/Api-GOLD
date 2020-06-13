<?php

namespace App\Http\Controllers\Api;

use App\Documento;
use App\Http\Controllers\Controller;
use App\PublicacionEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicacionesEmpresasController extends ApiController
{
    public function getPublicacionesEmpresas($empresa, $user)
    {
        $data = [];
        $id_publicaciones = [];
        $id_users = [];
        $publicaciones_empresas = DB::table("publicaciones_empresas")->where('empresa', $empresa)->get();
        foreach ($publicaciones_empresas as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }
        foreach ($publicaciones_empresas as $publicacion) {
            array_push($id_users, $publicacion->user);
        }
        $likes = DB::table("likes_empresas")->where('user', $user)->whereIn('publicacion_empresa', $id_publicaciones)->get();
        $documentos = DB::table("documentos")->whereIn('publicacion_empresa', $id_publicaciones)->get();
        $guardados = DB::table('mis_publicaciones_empresas')->where('user', $user)->whereIn('publicacion_empresa', $id_publicaciones)->get();
        $users = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->whereIn('users.id', $id_users)
            ->select("users.id", "users.user", "userdata.foto")
            ->get();
        foreach ($publicaciones_empresas as $publicacion) {
            $docs = "";
            $foto = "";
            $username = "";
            $favs = false;
            $save = false;
            foreach ($documentos as $doc) {
                if ($doc->publicacion_empresa == $publicacion->id) {
                    $docs = $doc->documento;
                }
            }
            foreach ($likes as $like) {
                if ($like->publicacion_empresa == $publicacion->id) {
                    $favs = true;
                }
            }
            foreach ($guardados as $guardado) {
                if ($guardado->publicacion_empresa == $publicacion->id) {
                    $save = true;
                }
            }
            foreach ($users as $user) {
                if ($user->id == $publicacion->user) {
                    $foto = $user->foto;
                    $username = $user->user;
                }
            }
            array_push($data, [
                "id" => $publicacion->id,
                "title" => $publicacion->title,
                "contenido" => $publicacion->contenido,
                "imagen" => $publicacion->foto,
                "created_at" => $publicacion->created_at,
                "documento" => $docs,
                "like" => $favs,
                "guardado" => $save,
                'foto' => $foto,
                'username' => $username
            ]);
        }

        return $this->sendResponse($data, "Publicaciones empresas recuperadas correctamente");
    }

    public function getPublicacionesEmpresasDetail($id, Request $request)
    {
        $publicaciones_empresas = PublicacionEmpresa::find($id);
        if ($publicaciones_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en empresa no existe"], 422);
        }

        $documento =Documento::where('publicacion_empresa', $id)->get();

        $data = [];
        $data["documento"] = $documento;
        $data["publicaciones_empresas"] = $publicaciones_empresas->find($id);

        return $this->sendResponse($data, "Datos de publicacion en empresa recuperados correctamente");
    }

    public function addPublicacionesEmpresas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'user' => 'required',
            'title' => 'required',
            'contenido' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $publicaciones_empresas = new PublicacionEmpresa();
        $documentos = new Documento();

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
            $path = $request->file('photo')->move(public_path("/imagenesPublicaciones/"), $fileName);
        }

        // Subir documentos
        if (!$request->hasFile('file')) {
            $name = NULL;
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $name = '';
            for ($i = 0; $i < 8; $i++) {
                $name .= $characters[rand(0, $charactersLength - 1)];
            }
            $file = $request->file('file');
            $path = public_path() . '/documentosEmpresas/';
            $name .= ' ';
            $name .= $file->getClientOriginalName();
            $file->move($path, $name);
        }

        $publicaciones_empresas->empresa = $request->get("empresa");
        $publicaciones_empresas->user = $request->get("user");
        $publicaciones_empresas->title = $request->get("title");
        $publicaciones_empresas->contenido = $request->get("contenido");
        $publicaciones_empresas->foto = $fileName;
        $publicaciones_empresas->save();

        if ($name != NULL) {
            $documentos->user = $request->get("user");
            $documentos->documento = $name;
            $documentos->publicacion_empresa = $publicaciones_empresas->id;
            $documentos->save();
        }

        $data = [
            'publicaciones_empresas' => $publicaciones_empresas,
            'documentos' => $documentos
        ];
        // Mail::to('jojanes01@live.com')->send(new SendMails());
        return $this->sendResponse($data, "Publicacion en empresa creada correctamente");
    }

    public function updatePublicacionesEmpresas(Request $request)
    {
        $publicaciones_empresas = PublicacionEmpresa::find($request->get("id"));
        if ($publicaciones_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en empresa no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'user' => 'required',
            'title' => 'required',
            'contenido' => 'required',
            'num_likes' => 'required',
            'foto' => 'required|image'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $fileName = '';
        for ($i = 0; $i < 20; $i++) {
            $fileName .= $characters[rand(0, $charactersLength - 1)];
        }
        $fileName .= '.jpg';
        $path = $request->file('foto')->move(public_path("/imagenesPublicaciones/"), $fileName);
        $photoURL = url('/' . $fileName);
        $publicaciones_empresas->empresa = $request->get("empresa");
        $publicaciones_empresas->user = $request->get("user");
        $publicaciones_empresas->title = $request->get("title");
        $publicaciones_empresas->contenido = $request->get("contenido");
        $publicaciones_empresas->num_likes = $request->get("num_likes");
        $publicaciones_empresas->foto = $photoURL;
        $publicaciones_empresas->update();

        $data = [
            'publicaciones_empresas' => $publicaciones_empresas
        ];
        return $this->sendResponse($data, "Publicacion en empresa modificada correctamente");
    }

    public function deletePublicacionesEmpresas(Request $request)
    {
        $publicaciones_empresas = PublicacionEmpresa::find($request->get("id"));
        if ($publicaciones_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en empresa no existe"], 422);
        }
        $publicaciones_empresas->delete();
        $data = [
            '$publicaciones_empresas' => $publicaciones_empresas
        ];
        return $this->sendResponse($data, "Publicacion en empresa eliminada correctamente");
    }
}
