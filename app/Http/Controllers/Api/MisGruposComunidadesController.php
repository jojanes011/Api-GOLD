<?php

namespace App\Http\Controllers\Api;

use App\Comunidad;
use App\Empresa;
use App\GrupoComunidad;
use App\Http\Controllers\Controller;
use App\MiComunidad;
use App\MiGrupoComunidad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MisGruposComunidadesController extends ApiController
{
    public function getMisGruposComunidades($user)
    {
        $data = [];
        $mis_grupos_comunidades = DB::table("mis_grupos_comunidades")
        ->where('user',$user)
        ->join('grupos_comunidades', 'mis_grupos_comunidades.grupo_comunidad', 'grupos_comunidades.id')
        ->select('grupos_comunidades.id','grupos_comunidades.nombre')
        ->get();
        $data['mis_grupos_comunidades'] = $mis_grupos_comunidades;

        return $this->sendResponse($data, "Mis grupos en comunidades recuperadas correctamente");
    }

    public function getMisGruposComunidadesDetail($id, Request $request)
    {
        $mis_grupos_comunidades = MiGrupoComunidad::find($id);
        if ($mis_grupos_comunidades === null) {
            return $this->sendError("No existe el dato en mi comunidad", ["No existe"], 422);
        }

        $grupo_comunidad = GrupoComunidad::find($mis_grupos_comunidades->grupo_comunidad);
        if ($grupo_comunidad === null) {
            return $this->sendError("Error en los datos", ["El grupo comunidad no existe"], 422);
        }

        $user = User::find($mis_grupos_comunidades->user);
        if ($user === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $mi_grupo_comunidad = DB::table("mis_grupos_comunidades")
            ->where("mis_grupos_comunidades.grupo_comunidad", "=", $mis_grupos_comunidades->grupo_comunidad)
            ->where("mis_grupos_comunidades.user", "=", $mis_grupos_comunidades->user)
            ->join("grupo_comunidad", "mis_comunidades.id", "grupos_comunidades.id")
            ->join("users", "mis_comunidades.id", "users.id")
            ->select("users.id as id_user", "users.user", "users.privilege", "grupos_comunidades.id as id_grupo_comunidad", "grupos_comunidades.foto as foto_grupo_comunidad", "grupos_comunidades.nombre", "grupos_comunidades.descripcion", "grupos_comunidades.creador", "grupos_comunidades.comunidad", "grupos_comunidades.created_at")
            ->get();

        $data = [
            'grupo_comunidad' => $grupo_comunidad,
            'user' => $user,
            'mi_grupo_comunidad' => $mi_grupo_comunidad
        ];
        return $this->sendResponse($data, "Mi grupo comunidad recuperada correctamente");
    }

    public function addMisGruposComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'grupo_comunidad' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_grupos_comunidades = new MiGrupoComunidad();
        $mis_grupos_comunidades->grupo_comunidad = $request->get("grupo_comunidad");
        $mis_grupos_comunidades->user = $request->get("user");
        $mis_grupos_comunidades->save();

        $data = [
            'mis_comunidades' => $mis_grupos_comunidades
        ];
        return $this->sendResponse($data, "Mi grupo comunidad vinculado correctamente");
    }

    public function updateMisGruposComunidades(Request $request)
    {
        $mis_grupos_comunidades = MiGrupoComunidad::find($request->get("id"));
        if ($mis_grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'grupo_comunidad' => 'required',
            'user' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $mis_grupos_comunidades->grupo_comunidad = $request->get("grupo_comunidad");
        $mis_grupos_comunidades->user = $request->get("user");
        $mis_grupos_comunidades->update();

        $data = [
            'mis_grupos_comunidades' => $mis_grupos_comunidades
        ];
        return $this->sendResponse($data, "Mi grupo comunidad modificado correctamente");
    }

    public function deleteMisGruposComunidades(Request $request)
    {
        $mis_grupos_comunidades = MiGrupoComunidad::find($request->get("id"));
        if ($mis_grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["No existe"], 422);
        }
        $mis_grupos_comunidades->delete();
        $data = [
            'mis_grupos_comunidades' => $mis_grupos_comunidades
        ];
        return $this->sendResponse($data, "Mi grupo comunidad eliminado correctamente");
    }

    public function getMiembrosGruposComunidades($id, Request $request)
    {
        $grupo_comunidad = GrupoComunidad::find($id);
        if ($grupo_comunidad === null) {
            return $this->sendError("Error en los datos", ["El grupo comunidad no existe"], 422);
        }

        $mi_grupo_comunidad = DB::table("mis_grupos_comunidades")
            ->where("mis_grupos_comunidades.grupo_comunidad", "=", $id)
            ->join("userdata", "mis_grupos_comunidades.user", "userdata.iduser")
            ->join("grupos_comunidades", "mis_grupos_comunidades.grupo_comunidad", "grupos_comunidades.id")
            ->select("userdata.iduser", "userdata.nombre", "userdata.primer_apellido", "userdata.segundo_apellido", "userdata.foto")
            ->get();

        $data = [
            'mis_grupos_comunidades' => $mi_grupo_comunidad
        ];
        return $this->sendResponse($data, "Miembros recuperados correctamente");
    }
}
