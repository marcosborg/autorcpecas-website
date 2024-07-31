<?php

namespace App\Http\Requests;

use App\Models\Conversation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateConversationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('conversation_edit');
    }

    public function rules()
    {
        return [
            'thread' => [
                'string',
                'nullable',
            ],
        ];
    }
}