@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.edit') }} {{ trans('cruds.emailLog.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.email-logs.update", [$emailLog->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label for="email">{{ trans('cruds.emailLog.fields.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $emailLog->email) }}">
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.emailLog.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="message">{{ trans('cruds.emailLog.fields.message') }}</label>
                            <textarea class="form-control" name="message" id="message" required>{{ old('message', $emailLog->message) }}</textarea>
                            @if($errors->has('message'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('message') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.emailLog.fields.message_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection