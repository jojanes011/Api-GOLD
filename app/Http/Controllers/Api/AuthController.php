<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function testOauth(Request $request)
    {
        $user = Auth::user();
        $userdata = Userdata::where('iduser','=',$user->id)->get()->first();

        $data = [
            'user' => $user,
            'userdata' => $userdata
        ];

        return $this->sendResponse($data, "Usuarios recuperados correctamente");
    }

    public function register (Request $request){

        $validator = Validator::make($request->all(), [
            'user' => 'required|unique:users',
            'privilegio' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'active' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Error de validación", $validator->errors(), 422);
        }

        $input = $request->all();
        $input["password"] = bcrypt($request->get("password"));
        $user = User::create($input);
        $token = $user->createToken("GoldApp")->accessToken;

        $data = [
            'token' => $token,
            'user' => $user
        ];
        return $this->sendResponse($data, "Usuarios recuperados correctamente");
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>
            'Successfully logged out']);
    }
}
