<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\MiEmpresa;
use App\User;
use App\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserdataController extends ApiController
{

    public function changePassword(){

    }

    public function getUsers()
    {
        $data = [];
        //$users = Userdata::all();
        $users = DB::table("users")
            ->join("userdata", "users.id", "=", "userdata.iduser")
            ->select("users.id", "users.email", "userdata.nombre", "userdata.foto", "userdata.fecha_nacimiento", "userdata.genero",  "userdata.identificacion", "userdata.celular", "userdata.primer_apellido", "userdata.segundo_apellido",)
            ->get();

        $data['users'] = $users;

        return $this->sendResponse($data, "Usuarios recuperados correctamente");
    }

    public function getAdmins()
    {
        $data = [];
        //$users = Userdata::all();
        $users = DB::table("users")->where("privilegio", "=", 2)
            ->select()
            ->get();

        $data['administradores'] = $users;

        return $this->sendResponse($data, "Administradores recuperados correctamente");
    }

    public function getUsersDetail($id, Request $request)
    {
        $user = new User();
        $userdata = Userdata::where("iduser", "=", $id)->first();
        $data = [];
        $data["user"] = $user->find($id);
        $data["userdata"] = $userdata;

        return $this->sendResponse($data, "Datos de usuario recuperados correctamente");
    }

    public function addUsers(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'fecha_nacimiento' => 'required',
            'genero' => 'required',
            'primer_apellido' => 'required',
            'segundo_apellido' => 'required',
            'celular' => 'required',
            'identificacion' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $input = $request->all();
        $input["password"] = bcrypt($request->get("password"));
        $user = User::create($input);
        $token = $user->createToken("MyApp")->accessToken;

        $userdata = new Userdata();
        $userdata->nombre = $request->get("name");
        $userdata->primer_apellido = $request->get("primer_apellido");
        $userdata->segundo_apellido = $request->get("segundo_apellido");
        $userdata->foto = $request->get("foto");
        $userdata->fecha_nacimiento = $request->get("fecha_nacimiento");
        $userdata->genero = $request->get("genero");
        $userdata->identificacion = $request->get("identificacion");
        $userdata->celular = $request->get("celular");
        $userdata->iduser = $user->id;
        $userdata->save();

        $data = [
            'token' => $token,
            'user' => $user,
            'userdata' => $userdata
        ];
        return $this->sendResponse($data, "Usuario creado correctamente");

    }

    public function completeProfile(Request $request){
        $user = User::find($request->get("id"));
        if($user === null){
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:userdata',
            'nombre' => 'required',
            'primer_apellido' => 'required',
            'segundo_apellido' => 'required',
            'fecha_nacimiento' => 'required',
            'genero' => 'required',
            'empresa' => 'required',
            'celular' => 'required',
            'identificacion' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        // Subir fotos
        if (!$request->hasFile('photo')) {
            $fileName = 'no-photo.png';
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
        $userdata = new Userdata();
        $userdata->nombre = $request->get("nombre");
        $userdata->primer_apellido = $request->get("primer_apellido");
        $userdata->segundo_apellido = $request->get("segundo_apellido");
        $userdata->foto = $fileName;
        $userdata->fecha_nacimiento = $request->get("fecha_nacimiento");
        $userdata->genero = $request->get("genero");
        $userdata->identificacion = $request->get("identificacion");
        $userdata->celular = $request->get("celular");
        $userdata->iduser = $request->get("id");
        $userdata->save();

        $mis_empresas = new MiEmpresa();
        $mis_empresas->empresa = $request->get('empresa');
        $mis_empresas->user = $request->get('id');
        $mis_empresas->aceptado = 0;
        $mis_empresas->save();


        $data = [
            'userdata' => $userdata,
            'user' => $user
        ];
        return $this->sendResponse($data, "Perfil completado correctamente");

    }

    public function updateUsers(Request $request){
        $user = User::find($request->get("id"));
        if($user === null){
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'active' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $user->active = $request->get("active");
        $user->save();

        $userdata = Userdata::where("iduser", "=", $request->get("id"))->first();
        $userdata->nombre = $request->get("nombre");
        $userdata->fecha_nacimiento = $request->get("fecha_nacimiento");
        $userdata->primer_apellido = $request->get("primer_apellido");
        $userdata->segundo_apellido = $request->get("segundo_apellido");
        $userdata->genero = $request->get("genero");
        $userdata->celular = $request->get("celular");
        $userdata->identificacion = $request->get("identificacion");
        $userdata->foto = $request->get("foto");
        $userdata->update();

        $data = [
            'user' => $user,
            'userdata' => $userdata
        ];
        return $this->sendResponse($data, "Usuario modificado correctamente");
    }

    public function updateProfile(Request $request){

        //  // Subir fotos
        //  if (!$request->hasFile('photo')) {
        //     $fileName = NULL;
        // } else {
        //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //     $charactersLength = strlen($characters);
        //     $fileName = '';
        //     for ($i = 0; $i < 20; $i++) {
        //         $fileName .= $characters[rand(0, $charactersLength - 1)];
        //     }
        //     $fileName .= '.jpg';
        //     $path = $request->file('photo')->move(public_path("/imagenesPerfil/"), $fileName);
        // }

        $userdata = Userdata::where("iduser", "=", $request->get("id"))->first();
        $userdata->nombre = $request->get("nombre");
        $userdata->primer_apellido = $request->get("primer_apellido");
        $userdata->segundo_apellido = $request->get("segundo_apellido");
        $userdata->celular = $request->get("celular");
        $userdata->identificacion = $request->get("identificacion");
        // $userdata->foto = $fileName;
        $userdata->update();

        $data = [
            'userdata' => $userdata
        ];
        return $this->sendResponse($data, "Usuario modificado correctamente");
    }

    public function deleteUsers(Request $request)
    {
        $user = User::find($request->get("id"));
        if ($user === null) {
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $user->delete();
        $userdata = Userdata::where("iduser", "=", $request->get("id"))->first();
        $userdata->delete();

        return $this->sendResponse([
            'status' => "OK"
        ], "Usuario borrado correctamente");
    }

    public function updateActive(Request $request)
    {
        $user = User::find($request->get("id"));
        if($user === null){
            return $this->sendError("Error en los datos", ["El usuario no existe"], 422);
        }

        $validator = Validator::make($request->all(), [
            'active' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $user->active = $request->get("active");
        $user->update();

        $data = [
            'user' => $user
        ];
        return $this->sendResponse($data, "Usuario modificado correctamente");
    }
}
