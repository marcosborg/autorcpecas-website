<?php

namespace App\Http\Requests;

use App\Models\Conversation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreConversationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('conversation_create');
    }

    public function rules()
    {
        return [
            'thread' => [
                'string',
                'required',
            ],
        ];
    }
}
