<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\OpenaiApi;
use App\Models\Conversation;
use App\Models\Message;

class ChatController extends Controller
{

    use OpenaiApi;

    public function startConversation(Request $request)
    {
        return $this->createThreadAndRun($request->message);
    }

    public function sendMessage(Request $request)
    {

        $thread_id = $request->thread_id;
        $message = $request->message;

        return $this->createMessage($thread_id, $message);
    }

    public function chat()
    {
        return view('chat');
    }

    public function addMessageToLog(Request $request)
    {
        if (!$request->conversation_id) {
            $conversation = new Conversation;
            $conversation->save();
            $conversation->thread = $conversation->id;
            $conversation->save();
            $message = new Message;
            $message->role = $request->role;
            $message->conversation_id = $conversation->id;
            $message->message = $request->message;
            $message->save();
            return $conversation;
        } else {
            $conversation = Conversation::find($request->conversation_id);
            if ($conversation) {
                $message = new Message;
                $message->role = $request->role;
                $message->conversation_id = $conversation->id;
                $message->message = $request->message;
                $message->save();
            }
        }
    }
}
