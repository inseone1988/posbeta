<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController
{

    public function getUsers(){
        $r = DB::select("SELECT id,email,username FROM users u");
        return response()->json(["success"=>true,"payload"=>$r]);
    }

}
