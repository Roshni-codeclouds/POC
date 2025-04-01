<?php

 namespace App\Http\Controller ;

 use Illuminate\Http\Request;
  use App\Models\User;
  use App\Models\Role;


  class RoleController extends Controller {

    public function assignRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $role = Role::where('name', $request->role)->firstOrFail();
        $user->roles()->attach($role);

        return response()->json(['message' => 'Role assigned successfully']);
    }

  }




?>