<?php

namespace App\Http\Controllers; // Fixed namespace

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;



class RoleController extends Controller
{
    public function assignRole(Request $request, $userId)
    {
        // Validate the request
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Find the user and role
        $user = User::findOrFail($userId);
        $role = Role::where('name', $request->role)->firstOrFail();

        // Assign role (avoid duplicates)
        $user->roles()->syncWithoutDetaching([$role->id]);

        return response()->json(['message' => 'Role assigned successfully']);
    }
}
