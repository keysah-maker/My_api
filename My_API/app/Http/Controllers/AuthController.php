<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\user;
use App\Models\token;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {

    public function login(Request $request) {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = $request->user();
                $data['token'] = $user->createToken('youtube_rattrapage')->accessToken;
                $data['name']  = $user->name;
                
                $newToken = new Token();
                $newToken->code = $data['token'];
                $newToken->user_id = $user->id;
                $newToken->expired_at = date("Y-m-d H:i:s", strtotime('+5 hours'));
                // $newToken->created_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
                $user->token = $newToken->code;
                $newToken->save();
                $user->save();
                unset($user->password);
    
                return response()->json(['message'=>'OK','data' => $newToken], 201);
            } else {
            //wrong login credentials, return, user not authorised to our system, return error code 401
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}