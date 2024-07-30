<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\OpenaiApi;

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
}
