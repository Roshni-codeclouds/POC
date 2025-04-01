<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Routing\Controller; // compulsory 

class AuthController extends Controller
{
    //

    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['register', 'login']]);
    // }
    // register 
    public function register (Request $request ){

        $validate = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6',
        ]);

        $defaultRole = Role::where('name' , 'User')->first();
        if (!$defaultRole) {
            return response()->json(['error' => 'User role not found. Run Role::insert() first.'], 500);
        }
      
    

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password'=>bcrypt($request->password),
        'role_id' => $defaultRole->id, // Assign default role

    ]);

    $token = auth('api')->login($user);
    // / Assign role to user (e.g., 'user' role)
    return $this->respondWithToken($token);

}


//login 
public function login (Request $request){
    $credentials = $request->only('email' , 'password');  // only to retrueve the details in an array

    if (!$token = JWTAuth:: attempt($credentials)){
         return response() -> json (['error'=> 'Unauthorized '], 401);
    }


    return $this->respondWithToken($token);
 
}

public function me()
{
    return response()->json(auth('api')->user());
}

// 🔹 LOGOUT USER
public function logout()
{
    auth()->logout();
    return response()->json(['message' => 'Successfully logged out']);
}

// 🔹 REFRESH TOKEN
public function refresh()
{
    return $this->respondWithToken(auth('api')->refresh());
}

// 🔹 HELPER: RESPOND WITH TOKEN
protected function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        'user' => auth()->user(),
    ]);
}

}

