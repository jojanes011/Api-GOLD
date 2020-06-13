<?php

namespace App\Http\Controllers\Api;

use App\ChatUsuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatsUsuariosController extends ApiController
{
    public function getChatsUsuarios($emisor, $receptor)
    {
        $data = [];

        $recibidos = DB::table("chats_usuarios")
        ->where([['emisor',$receptor],['receptor',$emisor]])
        ->orderBy('created_at', 'desc')
        ->get();

        if ($recibidos === null) {
            return $this->sendError("Error en los datos", ["El chat no existe"], 422);
        }
        $data_emisor = DB::table("userdata")->where('iduser',$emisor)->get();
        $data_receptor = DB::table("userdata")->where('iduser',$receptor)->get();

        $recibidos_array = [];
        foreach ($recibidos as $chat ) {
            array_push($recibidos_array, [
                '_id' => $chat->emisor,
                'text' => $chat->mensaje,
                'createdAt' => $chat->created_at,
                'user' => ['_id' => $data_receptor[0]->id, 'name' => $data_receptor[0]->nombre, 'avatar' => $data_receptor[0]->foto]
            ]);
        }

        $data = [
            // 'chats_usuarios' => $chats_usuarios,
            // 'emisor' => $data_emisor,
            // 'receptor' => $data_receptor,
            'chat' => $recibidos_array
        ];

        return $this->sendResponse($data, "Mensajes recuperados correctamente");
    }

    public function addChatsUsuarios(Request $request){
        $validator = Validator::make($request->all(), [
            'emisor' => 'required',
            'receptor' => 'required',
            'mensaje' => 'required',
            'leido' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $chats_usuarios = new ChatUsuario();
        $chats_usuarios->emisor = $request->get("emisor");
        $chats_usuarios->receptor = $request->get("receptor");
        $chats_usuarios->mensaje = $request->get("mensaje");
        $chats_usuarios->leido = $request->get("leido");
        $chats_usuarios->save();

        $data = [
            'chats_usuarios' => $chats_usuarios
        ];
        return $this->sendResponse($data, "Mensaje enviado correctamente");
    }
}
