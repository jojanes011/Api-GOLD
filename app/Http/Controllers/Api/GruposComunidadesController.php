<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\GrupoComunidad;

class GruposComunidadesController extends ApiController
{
    public function getGruposComunidades()
    {
        $data = [];
        $grupos_comunidades = DB::table("grupos_comunidades")
            ->join("comunidades", "grupos_comunidades.comunidad", "comunidades.id")
            ->select("grupos_comunidades.nombre", "grupos_comunidades.descripcion", "grupos_comunidades.foto", "grupos_comunidades.id", "comunidades.nombre as nombre_comunidad")
            ->orderBy('grupos_comunidades.created_at', 'desc')
            ->get();
        $data['grupos_comunidades'] = $grupos_comunidades;

        return $this->sendResponse($data, "Grupos en comunidades recuperadas correctamente");
    }

    public function getGruposComunidadesDetail($id, Request $request)
    {
        $grupos_comunidades = GrupoComunidad::find($id);
        if ($grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["El grupo en comunidades no existe"], 422);
        }

        $data = [];
        $data["grupos_comunidades"] = $grupos_comunidades->find($id);

        return $this->sendResponse($data, "Datos de grupo en comunidades recuperados correctamente");
    }

    public function getNoIntegrantesGrupoComunidad($grupo, $user, $comunidad)
    {
        $grupos_comunidades = GrupoComunidad::find($grupo);
        if ($grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["El grupo en comunidades no existe"], 422);
        }
        $empresas = DB::table('mis_comunidades')->where('comunidad', $comunidad)->distinct()->get();
        $array_empresa = [];
        foreach ($empresas as $empresa) {
            array_push($array_empresa, $empresa->empresa);
        }
        $users = DB::table('mis_empresas')->whereIn('empresa', $array_empresa)->distinct()->get();
        $array_user = [];
        foreach ($users as $user_id) {
            array_push($array_user, $user_id->user);
        }
        $integrantes = DB::table('mis_grupos_comunidades')->where('grupo_comunidad', $grupo)->get();
        $array_integrantes = [];
        foreach ($integrantes as $integrante) {
            array_push($array_integrantes, $integrante->id);
        }
        $noIntegrantes = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->whereIn('users.id', $array_user)
            ->whereNotIn('users.id', $array_integrantes)
            ->select("users.id", "users.email", "userdata.nombre", "userdata.foto", "userdata.fecha_nacimiento", "userdata.genero",  "userdata.identificacion", "userdata.celular", "userdata.primer_apellido", "userdata.segundo_apellido",)
            ->get();

        $data = [];
        $data["noIntegrantes"] = $noIntegrantes;
        $data["integrantes"] = $integrantes;
        $data["users"] = $users;


        return $this->sendResponse($data, "Datos de grupo en comunidades recuperados correctamente");
    }

    public function addGruposComunidades(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:grupos_comunidades',
            'descripcion' => 'required',
            'comunidad' => 'required',
            'creador' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

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
            $path = $request->file('photo')->move(public_path("/imagenesPublicaciones/"), $fileName);
        }

        $grupos_comunidades = new GrupoComunidad();
        $grupos_comunidades->nombre = $request->get("nombre");
        $grupos_comunidades->foto = $fileName;
        $grupos_comunidades->descripcion = $request->get("descripcion");
        $grupos_comunidades->comunidad = $request->get("comunidad");
        $grupos_comunidades->creador = $request->get("creador");
        $grupos_comunidades->save();

        $data = [
            'grupos_comunidades' => $grupos_comunidades
        ];
        return $this->sendResponse($data, "Grupo en comunidad creada correctamente");
    }

    public function updateGruposComunidades(Request $request)
    {
        $grupos_comunidades = GrupoComunidad::find($request->get("id"));
        if ($grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["El grupo en comunidades no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:grupos_comunidades',
            'foto' => 'required',
            'descripcion' => 'required',
            'comunidad' => 'required',
            'creador' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $grupos_comunidades->nombre = $request->get("nombre");
        $grupos_comunidades->foto = $request->get("foto");
        $grupos_comunidades->descripcion = $request->get("descripcion");
        $grupos_comunidades->comunidad = $request->get("comunidad");
        $grupos_comunidades->creador = $request->get("creador");
        $grupos_comunidades->update();

        $data = [
            'grupos_comunidades' => $grupos_comunidades
        ];
        return $this->sendResponse($data, "Grupo en comunidad modificada correctamente");
    }

    public function deleteGruposComunidades(Request $request)
    {
        $grupos_comunidades = GrupoComunidad::find($request->get("id"));
        if ($grupos_comunidades === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }
        $grupos_comunidades->delete();
        $data = [
            '$grupos_comunidades' => $grupos_comunidades
        ];
        return $this->sendResponse($data, "Grupo en comunidad eliminada correctamente");
    }
}
