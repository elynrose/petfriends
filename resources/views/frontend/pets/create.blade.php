@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.create') }} {{ trans('cruds.pet.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.pets.store") }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label class="required" for="photo">{{ trans('cruds.pet.fields.photo') }}</label>
                            <div class="needsclick dropzone" id="photo-dropzone">
                            </div>
                            @if($errors->has('photo'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('photo') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.photo_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required">{{ trans('cruds.pet.fields.type') }}</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Pet::TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('type'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('type') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.type_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.pet.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="age">{{ trans('cruds.pet.fields.age') }}</label>
                            <input class="form-control" type="number" name="age" id="age" value="{{ old('age', '') }}" step="1" required>
                            @if($errors->has('age'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('age') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.age_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required">{{ trans('cruds.pet.fields.gender') }}</label>
                            <select class="form-control" name="gender" id="gender" required>
                                <option value disabled {{ old('gender', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Pet::GENDER_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('gender', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('gender'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('gender') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.gender_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="notes">{{ trans('cruds.pet.fields.notes') }}</label>
                            <input class="form-control" type="text" name="notes" id="notes" value="{{ old('notes', '') }}">
                            @if($errors->has('notes'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('notes') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.notes_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <div>
                                <input type="hidden" name="not_available" value="0">
                                <input type="checkbox" name="not_available" id="not_available" value="1" {{ old('not_available', 0) == 1 ? 'checked' : '' }}>
                                <label for="not_available">{{ trans('cruds.pet.fields.not_available') }}</label>
                            </div>
                            @if($errors->has('not_available'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('not_available') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.not_available_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="from">{{ trans('cruds.pet.fields.from') }}</label>
                            <input class="form-control date" type="text" name="from" id="from" value="{{ old('from') }}">
                            @if($errors->has('from'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('from') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.from_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="from_time">{{ trans('cruds.pet.fields.from_time') }}</label>
                            <input class="form-control timepicker" type="text" name="from_time" id="from_time" value="{{ old('from_time') }}">
                            @if($errors->has('from_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('from_time') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.from_time_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="to">{{ trans('cruds.pet.fields.to') }}</label>
                            <input class="form-control date" type="text" name="to" id="to" value="{{ old('to') }}">
                            @if($errors->has('to'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('to') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.to_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="to_time">{{ trans('cruds.pet.fields.to_time') }}</label>
                            <input class="form-control timepicker" type="text" name="to_time" id="to_time" value="{{ old('to_time') }}">
                            @if($errors->has('to_time'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('to_time') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.pet.fields.to_time_helper') }}</span>
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
    var uploadedPhotoMap = {}
Dropzone.options.photoDropzone = {
    url: '{{ route('frontend.pets.storeMedia') }}',
    maxFilesize: 5, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="photo[]" value="' + response.name + '">')
      uploadedPhotoMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPhotoMap[file.name]
      }
      $('form').find('input[name="photo[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($pet) && $pet->photo)
      var files = {!! json_encode($pet->photo) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="photo[]" value="' + file.file_name + '">')
        }
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