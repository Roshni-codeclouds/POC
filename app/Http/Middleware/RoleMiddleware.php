<?php
namespace App\Http\Middleware;

use Closure;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Auth;


  class RoleMiddleware
  {
      public function handle(Request $request, Closure $next, $role)
      {
        if(!Auth::guard('api')->check()){
            return response()->json(['error'=> 'Unauthorized'], 401);
        }
    
        $user = Auth::guard('api')->user();

      if($user->role->name !== $role){
        return response()->json(['error' => 'Forbidden'], 403);
      }
      return $next($request);
    }


  }





?>