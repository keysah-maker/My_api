<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\user;
use App\Models\token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UserController extends Controller
{

    public function bearerToken()
    {
        $header = $this->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'string|required|regex:/^[a-zA-Z0-9_-]/|unique:user|max:255',
            'pseudo' => 'string|required',
            'email' => 'string|required|email|unique:user',
            'password'=> 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>"Bad Request", "code"=>10001, "data"=>$validator->errors()], 400);
        }

        $newUser = new user();
        $newUser->username = $request->username;
        $newUser->pseudo = $request->pseudo;
        $newUser->created_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
        $newUser->updated_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
        $newUser->email = $request->email;
        $newUser->password = $request->password;
        $newUser['password'] = bcrypt($newUser['password']);
        $newUser->save();

        unset($newUser->updated_at);
        unset($newUser->password);

        return response()->json(['message'=>'OK', 'data'=>$newUser], 201);
    }

    public function deleteUser(Request $request, $id)
    {
        $token = $request->bearerToken();
        $valid = token::where('code', $token)->first();
        if (user::where("id", $id)->first()) {
            $newUser = user::where("id", $id)->first();
        } else {
            return response()->json(['message' => 'Not found'], 404);  
        }
        if ($valid && $valid->user_id == $id && date("Y-m-d H:i:s") < $valid->expired_at == true) {
                $newUser->delete();
                return (response()->json([], 204));
        } else {
            return response()->json(['message'=>'Unauthorized'], 401); 
        }
    }

    public function updateUser(Request $request, $id)
    {
        $token = $request->bearerToken();
        $valid = token::where('code', $token)->first();
        if (user::where("id", $id)->first()) {
            $updateUser = user::where("id", $id)->first();
        } else {
            return response()->json(['message'=>'Not found'], 404);
        }
        if ($valid && $valid->user_id == $id && date("Y-m-d H:i:s") < $valid->expired_at == true) {

            $Validator = Validator::make($request->all(), [
            'username' => 'string|regex:/^[a-zA-Z0-9_-]/|unique:user|max:255',
            'pseudo' => 'string',
            'email' => 'string|email|unique:user',
            'password'=> 'string'
            ]);

            if ($Validator->fails()) {
                return response()->json(['message'=>"Bad Request", "code"=>10001, "data"=>$Validator->errors()], 400);
            }

                if ($request->username)
                    $updateUser->username = $request->username;
                if ($request->pseudo)
                    $updateUser->pseudo = $request->pseudo;
                if ($request->email)
                    $updateUser->email = $request->email;
                if ($request->password) {
                    $updateUser->password = $request->password;
                    $updateUser['password'] = bcrypt($updateUser['password']);
                }
           
                $updateUser->save();
                unset($updateUser->password);
                unset($updateUser->token);
                unset($updateUser->created_at);
                unset($updateUser->updated_at);

                return response()->json(['message'=>'OK', 'data'=> $updateUser], 200);
        } else {
            return response()->json(['message'=>'Unauthorized'], 401); 
        }
    }
}