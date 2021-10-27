<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\file;
use App\Models\token;
use App\Models\user;
use Illuminate\Support\Str;

class FileController extends Controller
{

    public function bearerToken()
    {
        $header = $this->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }
    }

    public function createFile(Request $request, $id)
    {
        $token = $request->bearerToken();
        $valid = token::where('code', $token)->first();
        if ($valid && $valid->user_id == $id && date("Y-m-d H:i:s") < $valid->expired_at == true) {

        $validator = Validator::make($request->all(), [
            'name' => 'string|required|regex:/^[a-zA-Z0-9_-]/|max:255',
            'source' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>"Bad Request", "code"=>10001, "data"=>$validator->errors()], 400);
        }

        $newFile = new file();
        $newFile->name = $request->name;
        $newFile->source = $request->source;
        $newFile->user_id = $id;
        $newFile->created_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
        $newFile->updated_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
        $newFile->save();

        unset($newFile->updated_at);
        unset($newFile->user_id);

        return response()->json(['message'=>'OK', 'data'=>$newFile], 201);
        } else {
            return response()->json(['message'=>'Unauthorized'], 401);
        }
    }

    public function listFile(Request $request, $id)
    {
        if (user::where("id", $id)) {
            $listFile = file::where("user_id", $id)->get();
            return response()->json(["message"=>"OK", "data"=>$listFile], 200);
        } else {
            return response()->json(['message' => 'Not found'], 404);
        }
    }

    public function updateFile(Request $request, $id)
    {
        $token = $request->bearerToken();
        $valid = token::where('code', $token)->first();
        if (file::where("id", $id)->first()) {
            $updateFile = file::where("id", $id)->first();
        } else {
            return response()->json(['message'=>'Not found'], 404);
        }
        if ($valid && $valid->user_id == $updateFile->user_id && date("Y-m-d H:i:s") < $valid->expired_at == true) {

            $Validator = Validator::make($request->all(), [
            'pseudo' => 'string',
            'password'=> 'string'
            ]);

            if ($Validator->fails()) {
                return response()->json(['message'=>"Bad Request", "code"=>10001, "data"=>$Validator->errors()], 400);
            }

                if ($request->name)
                    $updateFile->name = $request->name;
                if ($request->source)
                    $updateFile->source = $request->source;
                $updateFile->updated_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
                $updateFile->save();
                unset($updateFile->password);
                unset($updateFile->token);

                return response()->json(['message'=>'OK', 'data'=> $updateFile], 200);
            
        } else {
            return response()->json(['message'=>'Unauthorized'], 401); 
        }
    }

    public function deleteFile(Request $request, $id)
    {
        $token = $request->bearerToken();
        $valid = token::where('code', $token)->first();
        if (file::where("id", $id)->first()) {
            $newFile = file::where("id", $id)->first();
        } else {
            return response()->json(['message' => 'Not found'], 404);  
        }
        if ($valid && $valid->user_id == $newFile->user_id && date("Y-m-d H:i:s") < $valid->expired_at == true) {
                $newFile->delete();
                return (response()->json([], 204));
        } else {
            return response()->json(['message'=>'Unauthorized'], 401); 
        }
    }
}