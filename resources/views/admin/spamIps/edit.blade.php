@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.spamIp.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.spam-ips.update", [$spamIp->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="ip_address">{{ trans('cruds.spamIp.fields.ip_address') }}</label>
                <input class="form-control {{ $errors->has('ip_address') ? 'is-invalid' : '' }}" type="text" name="ip_address" id="ip_address" value="{{ old('ip_address', $spamIp->ip_address) }}">
                @if($errors->has('ip_address'))
                    <div class="invalid-feedback">
                        {{ $errors->first('ip_address') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.spamIp.fields.ip_address_helper') }}</span>
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