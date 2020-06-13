<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentosController extends ApiController
{
    public function getDocumentosComunidad($comunidad)
    {
        $data = [];
        $id_publicaciones = [];
        $publicaciones_comunidades = DB::table("publicaciones_comunidades")->where('comunidad', $comunidad)->get();
        foreach ($publicaciones_comunidades as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }

        $documentos = DB::table("documentos")->whereIn('publicacion_comunidad', $id_publicaciones)->get();

        $data['documentos'] = $documentos;

        return $this->sendResponse($data, "Documentos recuperados correctamente");
    }

    public function getDocumentosEmpresa($empresa)
    {
        $data = [];
        $id_publicaciones = [];
        $publicaciones_empresas = DB::table("publicaciones_empresas")->where('empresa', $empresa)->get();
        foreach ($publicaciones_empresas as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }

        $documentos = DB::table("documentos")->whereIn('publicacion_empresa', $id_publicaciones)->get();

        $data['documentos'] = $documentos;

        return $this->sendResponse($data, "Documentos recuperados correctamente");
    }

    public function getDocumentosGrupoComunidad($grupo_comunidad)
    {
        $data = [];
        $id_publicaciones = [];
        $publicaciones_grupos_comunidades = DB::table("publicaciones_grupos_comunidades")->where('grupo_comunidad', $grupo_comunidad)->get();
        foreach ($publicaciones_grupos_comunidades as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }

        $documentos = DB::table("documentos")->whereIn('publicacion_grupo_comunidad', $id_publicaciones)->get();

        $data['documentos'] = $documentos;

        return $this->sendResponse($data, "Documentos recuperados correctamente");
    }

    public function getDocumentosGrupoEmpresa($grupo_empresa)
    {
        $data = [];
        $id_publicaciones = [];
        $publicaciones_grupos_empresas = DB::table("publicaciones_grupos_empresas")->where('grupo_empresa', $grupo_empresa)->get();
        foreach ($publicaciones_grupos_empresas as $publicacion) {
            array_push($id_publicaciones, $publicacion->id);
        }

        $documentos = DB::table("documentos")->whereIn('publicacion_grupo_empresa', $id_publicaciones)->get();
        $data['documentos'] = $documentos;

        return $this->sendResponse($data, "Documentos recuperados correctamente");
    }
}
