@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.pet.title_singular') }}
    </div>

    <div class="card-body">
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route("admin.pets.update", [$pet->id]) }}" enctype="multipart/form-data" id="pet-form">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="photo">{{ trans('cruds.pet.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
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
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Pet::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $pet->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $pet->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="age">{{ trans('cruds.pet.fields.age') }}</label>
                <input class="form-control {{ $errors->has('age') ? 'is-invalid' : '' }}" type="number" name="age" id="age" value="{{ old('age', $pet->age) }}" step="1" required>
                @if($errors->has('age'))
                    <div class="invalid-feedback">
                        {{ $errors->first('age') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.age_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.pet.fields.gender') }}</label>
                <select class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender" id="gender" required>
                    <option value disabled {{ old('gender', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Pet::GENDER_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('gender', $pet->gender) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                <input class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" type="text" name="notes" id="notes" value="{{ old('notes', $pet->notes) }}">
                @if($errors->has('notes'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notes') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.notes_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('not_available') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="not_available" value="0">
                    <input class="form-check-input" type="checkbox" name="not_available" id="not_available" value="1" {{ $pet->not_available || old('not_available', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="not_available">{{ trans('cruds.pet.fields.not_available') }}</label>
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
                <input class="form-control date {{ $errors->has('from') ? 'is-invalid' : '' }}" type="text" name="from" id="from" value="{{ old('from', $pet->from) }}">
                @if($errors->has('from'))
                    <div class="invalid-feedback">
                        {{ $errors->first('from') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.from_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="from_time">{{ trans('cruds.pet.fields.from_time') }}</label>
                <input class="form-control timepicker {{ $errors->has('from_time') ? 'is-invalid' : '' }}" type="text" name="from_time" id="from_time" value="{{ old('from_time', $pet->from_time) }}">
                @if($errors->has('from_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('from_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.from_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="to">{{ trans('cruds.pet.fields.to') }}</label>
                <input class="form-control date {{ $errors->has('to') ? 'is-invalid' : '' }}" type="text" name="to" id="to" value="{{ old('to', $pet->to) }}">
                @if($errors->has('to'))
                    <div class="invalid-feedback">
                        {{ $errors->first('to') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.pet.fields.to_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="to_time">{{ trans('cruds.pet.fields.to_time') }}</label>
                <input class="form-control timepicker {{ $errors->has('to_time') ? 'is-invalid' : '' }}" type="text" name="to_time" id="to_time" value="{{ old('to_time', $pet->to_time) }}">
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

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize date pickers
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        // Initialize time pickers
        $('.timepicker').timepicker({
            format: 'HH:mm',
            showMeridian: false,
            minuteStep: 15
        });

        // Form validation
        $('#pet-form').on('submit', function(e) {
            var notAvailable = $('#not_available').is(':checked');
            
            if (!notAvailable) {
                var from = $('#from').val();
                var fromTime = $('#from_time').val();
                var to = $('#to').val();
                var toTime = $('#to_time').val();

                if (!from || !fromTime || !to || !toTime) {
                    e.preventDefault();
                    alert('Please fill in all availability fields when the pet is available.');
                    return false;
                }

                // Validate time range
                var start = moment(from + ' ' + fromTime);
                var end = moment(to + ' ' + toTime);

                if (end.isBefore(start)) {
                    e.preventDefault();
                    alert('End date/time must be after start date/time.');
                    return false;
                }

                // Validate business hours (6 AM to 10 PM)
                var startHour = start.hour();
                var endHour = end.hour();

                if (startHour < 6 || startHour >= 22 || endHour < 6 || endHour >= 22) {
                    e.preventDefault();
                    alert('Bookings are only allowed between 6 AM and 10 PM.');
                    return false;
                }
            }
        });

        // Toggle availability fields
        $('#not_available').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('#from, #from_time, #to, #to_time').prop('disabled', isChecked);
        }).trigger('change');
    });

    var uploadedPhotoMap = {}
Dropzone.options.photoDropzone = {
    url: '{{ route('admin.pets.storeMedia') }}',
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