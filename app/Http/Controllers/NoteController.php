<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\User;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use PhpParser\Node\Stmt\ElseIf_;
use Illuminate\Support\Facades\Cache;



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
    $date = $request->input('date', Carbon::now()->toDateString());
    $isToday = $date === Carbon::now()->toDateString();

    $startDay = Carbon::parse($date, 'Asia/Kolkata')->startOfDay()->setTimezone('UTC')->toDateTimeString();
    $endDay = Carbon::parse($date, 'Asia/Kolkata')->endOfDay()->setTimezone('UTC')->toDateTimeString();

    $user_id = $request->input('user_id'); // For filtering specific user’s notes (admin only)

    
    $cacheKey = "notes_{$user_id}_{$date}";

    if ($isToday) {
        
        $query = Note::whereBetween('created_at', [$startDay, $endDay]);

        if ($user->role->name !== 'admin') {
         
            $query->where('user_id', $user->id);
        } else {
            //  Admin should see all  notes unless a specific user_id is provided
            if (!empty($user_id)) {
                $query->where('user_id', $user_id);
            }
        }

        $notes = $query->get();
    } else {
        //  notes ka time limit is being set to 24 hrs only 
        $notes = Cache::remember($cacheKey, 86400, function () use ($startDay, $endDay, $user, $user_id) {
            $query = Note::whereBetween('created_at', [$startDay, $endDay]);

            if ($user->role->name !== 'admin') {
                $query->where('user_id', $user->id);
            } else {
                if (!empty($user_id)) {
                    $query->where('user_id', $user_id);
                }
            }

            return $query->get();
        });
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
        // conversion of timstamp
    $note->created_at = Carbon::parse($note->created_at)->setTimezone('Asia/Kolkata')->toDateTimeString();
    $note->updated_at = Carbon::parse($note->updated_at)->setTimezone('Asia/Kolkata')->toDateTimeString();
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
