@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.conversation.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.conversations.update", [$conversation->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="thread">{{ trans('cruds.conversation.fields.thread') }}</label>
                <input class="form-control {{ $errors->has('thread') ? 'is-invalid' : '' }}" type="text" name="thread" id="thread" value="{{ old('thread', $conversation->thread) }}" required>
                @if($errors->has('thread'))
                    <div class="invalid-feedback">
                        {{ $errors->first('thread') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.conversation.fields.thread_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection