<?php

namespace App\Http\Controllers\Api;

use App\Documento;
use App\Http\Controllers\Controller;
use App\PublicacionGrupoComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicacionesGruposComunidadesController extends ApiController
{
    public function getPublicacionesGruposComunidades($grupo, $user)
    {
        $data = [];
        $id_publicaciones = [];
        $id_users = [];
        $publicaciones_grupos_comunidades = DB::table("publicaciones_grupos_comunidades")->where('grupo_comunidad', $grupo)->get();
        foreach ($publicaciones_grupos_comunidades as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }
        foreach ($publicaciones_grupos_comunidades as $publicacion) {
            array_push($id_users, $publicacion->user);
        }
        $likes = DB::table("likes_grupos_comunidades")->where('user', $user)->whereIn('publicacion_grupo_comunidad', $id_publicaciones)->get();
        $documentos = DB::table("documentos")->whereIn('publicacion_grupo_comunidad', $id_publicaciones)->get();
        $guardados = DB::table('mis_publicaciones_grupos_comunidades')->where('user', $user)->whereIn('publi_gru_comunidad', $id_publicaciones)->get();
        $users = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->whereIn('users.id', $id_users)
            ->select("users.id", "users.user", "userdata.foto")
            ->get();
        foreach ($publicaciones_grupos_comunidades as $publicacion) {
            $docs = "";
            $foto = "";
            $username = "";
            $favs = false;
            $save = false;
            foreach ($documentos as $doc) {
                if ($doc->publicacion_grupo_comunidad == $publicacion->id) {
                    $docs = $doc->documento;
                }
            }
            foreach ($likes as $like) {
                if ($like->publicacion_grupo_comunidad == $publicacion->id) {
                    $favs = true;
                }
            }
            foreach ($guardados as $guardado) {
                if ($guardado->publi_gru_comunidad == $publicacion->id) {
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

        return $this->sendResponse($data, "Publicaciones grupo comunidad recuperadas correctamente");
    }

    public function getPublicacionesGruposComunidadesDetail($id, Request $request)
    {
        $publicaciones_grupos_comunidades = PublicacionGrupoComunidad::find($id);
        if ($publicaciones_grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }

        $documento = Documento::where('publicacion_grupo_comunidad', $id)->get();

        $data = [];
        $data["documento"] = $documento;
        $data["publicaciones_grupos_comunidades"] = $publicaciones_grupos_comunidades->find($id);

        return $this->sendResponse($data, "Datos de publicacion en comunidad recuperados correctamente");
    }

    public function addPublicacionesGruposComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_comunidad' => 'required',
            'user' => 'required',
            'title' => 'required',
            'contenido' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $publicaciones_grupos_comunidades = new PublicacionGrupoComunidad();
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
            $path = public_path() . '/documentosComunidades/';
            $name .= ' ';
            $name .= $file->getClientOriginalName();
            $file->move($path, $name);
        }

        $publicaciones_grupos_comunidades->grupo_comunidad = $request->get("grupo_comunidad");
        $publicaciones_grupos_comunidades->user = $request->get("user");
        $publicaciones_grupos_comunidades->title = $request->get("title");
        $publicaciones_grupos_comunidades->contenido = $request->get("contenido");
        $publicaciones_grupos_comunidades->foto = $fileName;
        $publicaciones_grupos_comunidades->save();

        if ($name != NULL) {
            $documentos->user = $request->get("user");
            $documentos->documento = $name;
            $documentos->publicacion_grupo_comunidad = $publicaciones_grupos_comunidades->id;
            $documentos->save();
        }

        $data = [
            'publicaciones_grupos_comunidades' => $publicaciones_grupos_comunidades,
            'documentos' => $documentos
        ];
        // Mail::to('jojanes01@live.com')->send(new SendMails());
        return $this->sendResponse($data, "Publicacion en grupo comunidad creada correctamente");
    }

    public function updatePublicacionesGruposComunidades(Request $request)
    {
        $publicaciones_grupos_comunidades = PublicacionGrupoComunidad::find($request->get("id"));
        if ($publicaciones_grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en el grupo comunidad no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'grupo_comunidad' => 'required',
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
        $publicaciones_grupos_comunidades->comunidad = $request->get("grupo_comunidad");
        $publicaciones_grupos_comunidades->user = $request->get("user");
        $publicaciones_grupos_comunidades->title = $request->get("title");
        $publicaciones_grupos_comunidades->contenido = $request->get("contenido");
        $publicaciones_grupos_comunidades->foto = $photoURL;
        $publicaciones_grupos_comunidades->update();

        $data = [
            'publicaciones_grupos_comunidades' => $publicaciones_grupos_comunidades
        ];
        return $this->sendResponse($data, "Publicacion en grupo comunidad modificada correctamente");
    }

    public function deletePublicacionesGruposComunidades(Request $request)
    {
        $publicaciones_grupos_comunidades = PublicacionGrupoComunidad::find($request->get("id"));
        if ($publicaciones_grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }
        $publicaciones_grupos_comunidades->delete();
        $data = [
            '$publicaciones_grupos_comunidades' => $publicaciones_grupos_comunidades
        ];
        return $this->sendResponse($data, "Publicacion en grupo comunidad eliminada correctamente");
    }
}
