<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\User;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use PhpParser\Node\Stmt\ElseIf_;



class NoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createForUser(Request $request)
    {
        $admin = Auth::user();

        if ($admin->role->name !== 'admin') {
            return response()->json(['error' => 'Unauthorized - Admins Can create only']);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        /// crete notes 
        $note = Note::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'created_at' => Carbon::now('Asia/Kolkata')->setTimezone('UTC'),
            'updated_at' => Carbon::now('Asia/Kolkata')->setTimezone('UTC'),
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
                'created_at' => Carbon::now('Asia/Kolkata')->setTimezone('UTC'),
                'updated_at' => Carbon::now('Asia/Kolkata')->setTimezone('UTC'),
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


    public function index(Request $request)
    {
        $user = Auth::user();

        // default mai aaj ka date jayega 

        $date = $request->input('date', now()->toDateString());
        // $utcDate = Carbon::parse($date, 'Asia/Kolkata')->setTimezone('UTC')->toDateString();

        $startDay = date('Y-m-d H:i:s' , strtotime($date . '00:00:00 UTC'));
        $endDay = date('Y-m-d H:i:s' , strtotime($date . '23:59:59 UTC'));


        // to  get the date between 
        // defaultt user id
        $user_id = $request->input('user_id', $user->id);

        if ($user->role->name === 'admin') {
            $query = Note::whereBetween('created_at', [$startDay , $endDay]);
                // ->where('user_id', $user_id) // Filter by user_id
         
        
        if($user_id){
        $query = Note::where('user_id', $user_id);
                  }

                  $notes=$query->get();
                }
         else {
            // Regular users can only fetch their own notes for the same day
            $notes = Note::whereBetween('created_at',  [$startDay , $endDay])
                ->where('user_id', $user->id) // Only fetch their own notes
                ->get();
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

        if ($user->role->name !== 'admin' && $note->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        //conversion of timstamp
    // $note->created_at = Carbon::parse($note->created_at)->setTimezone('Asia/Kolkata')->toDateTimeString();
    // $note->updated_at = Carbon::parse($note->updated_at)->setTimezone('Asia/Kolkata')->toDateTimeString();
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
