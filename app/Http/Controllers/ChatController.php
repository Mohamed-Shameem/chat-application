<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        // Ensure all routes are protected by the 'auth' middleware
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chat');
    }

    public function fetchMessages()
    {
        return Chat::all();
    }

    public function sendMessage(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Validate the request data
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Create the chat message with the authenticated user's name
        $chat = Chat::create([
            'user' => $user->name,
            'message' => $request->message
        ]);

        // Broadcast the message
         /* broadcast(new MessageSent($chat))->toOthers(); */
        event(new MessageSent($chat));

       return response()->json(['chat' => $chat], 200);
    }
}
