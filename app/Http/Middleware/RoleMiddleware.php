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
    
        // $user = Auth::guard('api')->user();

        $admin = Auth::user();

      
    //Check if the authenticated user is an admin
  //   if (!$user || $user->role->name !== $role) { {
  //     return response()->json(['error' => 'Unauthorized - Admins Can create only'], 403);
  // }


  if ($admin->role->name!== 'admin') {
    return response()->json(['error' => 'Unauthorized - Admins Can create only']);
}
      return $next($request);
    }


  
  }




?>