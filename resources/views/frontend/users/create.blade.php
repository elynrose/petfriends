@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.users.store") }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label for="photo">{{ trans('cruds.user.fields.photo') }}</label>
                            <div class="needsclick dropzone" id="photo-dropzone">
                            </div>
                            @if($errors->has('photo'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('photo') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.photo_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="first_name">{{ trans('cruds.user.fields.first_name') }}</label>
                            <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name', '') }}" required>
                            @if($errors->has('first_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('first_name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.first_name_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="last_name">{{ trans('cruds.user.fields.last_name') }}</label>
                            <input class="form-control" type="text" name="last_name" id="last_name" value="{{ old('last_name', '') }}" required>
                            @if($errors->has('last_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('last_name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.last_name_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="street_address">{{ trans('cruds.user.fields.street_address') }}</label>
                            <input class="form-control" type="text" name="street_address" id="street_address" value="{{ old('street_address', '') }}">
                            @if($errors->has('street_address'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('street_address') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.street_address_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('cruds.user.fields.state') }}</label>
                            <select class="form-control" name="state" id="state">
                                <option value disabled {{ old('state', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\User::STATE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('state', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('state'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('state') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.state_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="city">{{ trans('cruds.user.fields.city') }}</label>
                            <input class="form-control" type="text" name="city" id="city" value="{{ old('city', '') }}">
                            @if($errors->has('city'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('city') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.city_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="zip_code">{{ trans('cruds.user.fields.zip_code') }}</label>
                            <input class="form-control" type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', '') }}" required>
                            @if($errors->has('zip_code'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('zip_code') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.zip_code_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" required>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                            <input class="form-control" type="password" name="password" id="password" required>
                            @if($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2" name="roles[]" id="roles" multiple required>
                                @foreach($roles as $id => $role)
                                    <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('roles'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('roles') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <div>
                                <input type="hidden" name="agree_to_terms" value="0">
                                <input type="checkbox" name="agree_to_terms" id="agree_to_terms" value="1" {{ old('agree_to_terms', 0) == 1 ? 'checked' : '' }}>
                                <label for="agree_to_terms">{{ trans('cruds.user.fields.agree_to_terms') }}</label>
                            </div>
                            @if($errors->has('agree_to_terms'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('agree_to_terms') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.agree_to_terms_helper') }}</span>
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

@section('scripts')
<script>
    Dropzone.options.photoDropzone = {
    url: '{{ route('frontend.users.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="photo"]').remove()
      $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="photo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->photo)
      var file = {!! json_encode($user->photo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection