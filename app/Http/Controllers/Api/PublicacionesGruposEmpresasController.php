<?php

namespace App\Http\Controllers\Api;

use App\Documento;
use App\Http\Controllers\Controller;
use App\PublicacionGrupoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicacionesGruposEmpresasController extends ApiController
{
    public function getPublicacionesGruposEmpresas($grupo, $user)
    {
        $data = [];
        $id_publicaciones = [];
        $id_users = [];
        $publicaciones_grupos_empresas = DB::table("publicaciones_grupos_empresas")->where('grupo_empresa', $grupo)->get();
        foreach ($publicaciones_grupos_empresas as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }
        foreach ($publicaciones_grupos_empresas as $publicacion) {
            array_push($id_users, $publicacion->user);
        }
        $likes = DB::table("likes_grupos_empresas")->where('user', $user)->whereIn('publicacion_grupo_empresa', $id_publicaciones)->get();
        $documentos = DB::table("documentos")->whereIn('publicacion_grupo_empresa', $id_publicaciones)->get();
        $guardados = DB::table('mis_publicaciones_grupos_empresas')->where('user', $user)->whereIn('publi_gru_empresa', $id_publicaciones)->get();
        $users = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->whereIn('users.id', $id_users)
            ->select("users.id", "users.user", "userdata.foto")
            ->get();
        foreach ($publicaciones_grupos_empresas as $publicacion) {
            $docs = "";
            $foto = "";
            $username = "";
            $favs = false;
            $save = false;
            foreach ($documentos as $doc) {
                if ($doc->publicacion_grupo_empresa == $publicacion->id) {
                    $docs = $doc->documento;
                }
            }
            foreach ($likes as $like) {
                if ($like->publicacion_grupo_empresa == $publicacion->id) {
                    $favs = true;
                }
            }
            foreach ($guardados as $guardado) {
                if ($guardado->publi_gru_empresa == $publicacion->id) {
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

        return $this->sendResponse($data, "Publicaciones grupo empresa recuperadas correctamente");
    }

    public function getPublicacionesGruposEmpresasDetail($id, Request $request)
    {
        $publicaciones_grupos_empresas = PublicacionGrupoEmpresa::find($id);
        if ($publicaciones_grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en grupo empresa no existe"], 422);
        }

        $documento = Documento::where('publicacion_grupo_empresa', $id)->get();

        $data = [];
        $data["documento"] = $documento;
        $data["publicaciones_grupos_empresas"] = $publicaciones_grupos_empresas->find($id);

        return $this->sendResponse($data, "Datos de publicacion en empresa recuperados correctamente");
    }

    public function addPublicacionesGruposEmpresas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_empresa' => 'required',
            'user' => 'required',
            'title' => 'required',
            'contenido' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $publicaciones_grupos_empresas = new PublicacionGrupoEmpresa();
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

        $publicaciones_grupos_empresas->grupo_empresa = $request->get("grupo_empresa");
        $publicaciones_grupos_empresas->user = $request->get("user");
        $publicaciones_grupos_empresas->title = $request->get("title");
        $publicaciones_grupos_empresas->contenido = $request->get("contenido");
        $publicaciones_grupos_empresas->foto = $fileName;
        $publicaciones_grupos_empresas->save();

        if ($name != NULL) {
            $documentos->user = $request->get("user");
            $documentos->documento = $name;
            $documentos->publicacion_grupo_empresa = $publicaciones_grupos_empresas->id;
            $documentos->save();
        }

        $data = [
            'publicaciones_grupos_empresas' => $publicaciones_grupos_empresas,
            'documentos' => $documentos
        ];
        // Mail::to('jojanes01@live.com')->send(new SendMails());
        return $this->sendResponse($data, "Publicacion en grupo empresa creada correctamente");
    }

    public function updatePublicacionesEmpresas(Request $request)
    {
        $publicaciones_grupos_empresas = PublicacionGrupoEmpresa::find($request->get("id"));
        if ($publicaciones_grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en el grupo empresa no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'grupo_empresa' => 'required',
            'user' => 'required',
            'title' => 'required',
            'contenido' => 'required',
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
        $publicaciones_grupos_empresas->empresa = $request->get("grupo_empresa");
        $publicaciones_grupos_empresas->user = $request->get("user");
        $publicaciones_grupos_empresas->title = $request->get("title");
        $publicaciones_grupos_empresas->contenido = $request->get("contenido");
        $publicaciones_grupos_empresas->foto = $photoURL;
        $publicaciones_grupos_empresas->update();

        $data = [
            'publicaciones_grupos_empresas' => $publicaciones_grupos_empresas
        ];
        return $this->sendResponse($data, "Publicacion en grupo empresa modificada correctamente");
    }

    public function deletePublicacionesEmpresas(Request $request)
    {
        $publicaciones_grupos_empresas = PublicacionGrupoEmpresa::find($request->get("id"));
        if ($publicaciones_grupos_empresas === null) {
            return $this->sendError("Error en los datos", ["La publicacion en empresa no existe"], 422);
        }
        $publicaciones_grupos_empresas->delete();
        $data = [
            '$publicaciones_grupos_empresas' => $publicaciones_grupos_empresas
        ];
        return $this->sendResponse($data, "Publicacion en grupo empresa eliminada correctamente");
    }
}
