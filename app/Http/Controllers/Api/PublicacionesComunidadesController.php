<?php

namespace App\Http\Controllers\Api;

use App\Documento;
use App\Http\Controllers\Controller;
use App\Mail\SendMails;
use App\PublicacionComunidad;
use App\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PublicacionesComunidadesController extends ApiController
{
    public function getPublicacionesComunidades($comunidad, $user)
    {
        $data = [];
        $id_publicaciones = [];
        $id_users = [];
        $publicaciones_comunidades = DB::table("publicaciones_comunidades")->where('comunidad', $comunidad)->get();
        foreach ($publicaciones_comunidades as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }
        foreach ($publicaciones_comunidades as $publicacion) {
            array_push($id_users, $publicacion->user);
        }
        $likes = DB::table("likes_comunidades")->where('user', $user)->whereIn('publicacion_comunidad', $id_publicaciones)->get();
        $documentos = DB::table("documentos")->whereIn('publicacion_comunidad', $id_publicaciones)->get();
        $guardados = DB::table('mis_publicaciones_comunidades')->where('user', $user)->whereIn('publicacion_comunidad', $id_publicaciones)->get();
        $users = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->whereIn('users.id', $id_users)
            ->select("users.id", "userdata.nombre","userdata.primer_apellido", "userdata.foto")
            ->get();
        foreach ($publicaciones_comunidades as $publicacion) {
            $docs = "";
            $foto = "";
            $username = "";
            $favs = false;
            $save = false;
            foreach ($documentos as $doc) {
                if ($doc->publicacion_comunidad == $publicacion->id) {
                    $docs = $doc->documento;
                }
            }
            foreach ($likes as $like) {
                if ($like->publicacion_comunidad == $publicacion->id) {
                    $favs = true;
                }
            }
            foreach ($guardados as $guardado) {
                if ($guardado->publicacion_comunidad == $publicacion->id) {
                    $save = true;
                }
            }
            foreach ($users as $user) {
                if ($user->id == $publicacion->user) {
                    $foto = $user->foto;
                    $username = $user->nombre . ' ' . $user->primer_apellido;
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

        return $this->sendResponse($data, "Publicaciones comunidades recuperadas correctamente");
    }

    public function getPublicacionesComunidadesDetail($id, Request $request)
    {
        $publicaciones_comunidades = PublicacionComunidad::find($id);
        if ($publicaciones_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }

        $user = Userdata::where('iduser','=',$publicaciones_comunidades->user)->first();
        $documento = Documento::where('publicacion_comunidad', '=', $id)->first();
        $empresa = DB::table('mis_empresas')
            ->where('user', $user->iduser)
            ->join('empresas','empresas.id','mis_empresas.empresa')
            ->select('empresas.nombre','empresas.id')
            ->get();

        $data = [];
        $data["documento"] = $documento;
        $data["user"] = $user;
        $data['empresa'] = $empresa;
        $data["publicaciones_comunidades"] = $publicaciones_comunidades->find($id);

        return $this->sendResponse($data, "Datos de publicacion en comunidad recuperados correctamente");
    }

    public function addPublicacionesComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comunidad' => 'required',
            'user' => 'required',
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }


        $publicaciones_comunidades = new PublicacionComunidad();
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

        $publicaciones_comunidades->comunidad = $request->get("comunidad");
        $publicaciones_comunidades->user = $request->get("user");
        $publicaciones_comunidades->title = $request->get("title");
        $publicaciones_comunidades->contenido = $request->get("contenido");
        $publicaciones_comunidades->foto = $fileName;
        $publicaciones_comunidades->save();

        if ($name != NULL) {
            $documentos->user = $request->get("user");
            $documentos->documento = $name;
            $documentos->publicacion_comunidad = $publicaciones_comunidades->id;
            $documentos->save();
        }

        $data = [
            'publicaciones_comunidades' => $publicaciones_comunidades,
            'documentos' => $documentos
        ];
        // Mail::to('jojanes01@live.com')->send(new SendMails());
        return $this->sendResponse($data, "Publicacion en comunidad creada correctamente");
    }

    public function updatePublicacionesComunidades(Request $request)
    {
        $publicaciones_comunidades = PublicacionComunidad::find($request->get("id"));
        if ($publicaciones_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'comunidad' => 'required',
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
        $publicaciones_comunidades->comunidad = $request->get("comunidad");
        $publicaciones_comunidades->user = $request->get("user");
        $publicaciones_comunidades->title = $request->get("title");
        $publicaciones_comunidades->contenido = $request->get("contenido");
        $publicaciones_comunidades->num_likes = $request->get("num_likes");
        $publicaciones_comunidades->foto = $photoURL;
        $publicaciones_comunidades->update();

        $data = [
            'publicaciones_comunidades' => $publicaciones_comunidades
        ];
        return $this->sendResponse($data, "Publicacion en comunidad modificada correctamente");
    }

    public function deletePublicacionesComunidades(Request $request)
    {
        $publicaciones_comunidades = PublicacionComunidad::find($request->get("id"));
        if ($publicaciones_comunidades === null) {
            return $this->sendError("Error en los datos", ["La publicacion en comunidad no existe"], 422);
        }
        $publicaciones_comunidades->delete();
        $data = [
            '$publicaciones_comunidades' => $publicaciones_comunidades
        ];
        return $this->sendResponse($data, "Publicacion en comunidad eliminada correctamente");
    }
}
