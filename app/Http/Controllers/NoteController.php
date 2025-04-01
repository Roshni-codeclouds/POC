<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\User;
use Illuminate\Routing\Controller; 



class NoteController extends Controller
{
    
     public function __construct()
     {
         $this->middleware('auth:api');
     }

   public function createForUser(Request $request , $user_id){
    $admin = Auth::user();

    if($admin->role->name !== 'Admin'){
        return response()->json(['error'=> 'Unauthorized - Admins Can create only']);
    }
    $request->validate([
        'content' => 'required|string'
    ]);

    $user = User::find($user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

   /// crete notes 
        $note = Note::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Note created successfully', 'note' => $note], 201);

   }


   // for users 

   public function createForSelf(Request $request)
   {
        // Get the authenticated user
    $user = Auth::user();

    // Validate the request inputs
    $request->validate([
          // Validate title
        'content' => 'required|string',         // Validate content
    ]);

    try {
        // Create the note for the authenticated user
        $note = Note::create([
            'user_id' => $user->id,               // Link note to the user
          // Store the title
            'content' => $request->content,       // Store the content
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Note created successfully',
            'note' => $note
        ], 201);  // Status code 201 for resource creation

    } catch (\Exception $e) {
        // Return an error response if note creation fails
        return response()->json([
            'message' => 'Failed to create note',
            'error' => $e->getMessage()
        ], 500);  // Status code 500 for internal server error
    }
   }


    public function index()
    {
        $user = Auth::user();

        if ($user->role->name === 'Admin') {
            $notes = Note::all();
        } else {
            $notes = Note::where('user_id', $user->id)->get();
        }

        return response()->json($notes);
    }

    // 🔹 View a specific note
    public function show($id)
    {
        $user = Auth::user();
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['error' => 'Note not found'], 404);
        }

        if ($user->role->name !== 'Admin' && $note->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($note);
    }



    public function destroy($id)
    {
        $user = Auth::user();
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['error' => 'Note not found'], 404);
        }

        if ($user->role->name !== 'Admin' && $note->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }
}
