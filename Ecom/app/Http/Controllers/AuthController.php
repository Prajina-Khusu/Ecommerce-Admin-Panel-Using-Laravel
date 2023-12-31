<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   public function login()
   {

   }

   public function register()
   {
    return view('front.account.register');
   }


   public function processRegister(Request $request)
   {
    $validator=Validator::make($request->all(),[
        'name'=>'required|min:3',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:5'
    ]);
    if($validator->passes())
    {

    }
    else
    {
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }
   }
}
