<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        /*User::create([
                "name"=>"admin",
                "email"=>"admin",
                "password"=>\Hash::make("123456")
            ]
        );*/
        return view("admin.login");
    }

    public function login( Request $request)
    {
        $username = $request->username;
        $password= $request->password;

        $attemp = [
            "email"=>$username,
            "password"=>$password
        ];

        if(Auth::attempt($attemp)){
            return response()->json([
                "status"=> true
            ]);
        }else{
            return response()->json([
                "status"=> false
            ]);
        }
    }

}