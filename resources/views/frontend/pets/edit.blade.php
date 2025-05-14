@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif  
            
            <div class="card">
                <div class="card-header">
                    {{ trans('global.edit') }} {{ trans('cruds.pet.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.pets.update", [$pet->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="photo">{{ trans('cruds.pet.fields.photo') }}</label>
                                    <div class="needsclick dropzone" id="photo-dropzone">
                                    </div>
                                    @if($errors->has('photo'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('photo') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="name">{{ trans('cruds.pet.fields.name') }}</label>
                                    <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $pet->name) }}" required>
                                    @if($errors->has('name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="required">{{ trans('cruds.pet.fields.type') }}</label>
                                    <select class="form-control" name="type" id="type" required>
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="age">{{ trans('cruds.pet.fields.age') }}</label>
                                    <input class="form-control" type="number" name="age" id="age" value="{{ old('age', $pet->age) }}" step="1" required>
                                    @if($errors->has('age'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('age') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">{{ trans('cruds.pet.fields.gender') }}</label>
                                    <select class="form-control" name="gender" id="gender" required>
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">{{ trans('cruds.pet.fields.notes') }}</label>
                                    <textarea class="form-control" name="notes" id="notes" rows="2">{{ old('notes', $pet->notes) }}</textarea>
                                    @if($errors->has('notes'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('notes') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="not_available" value="0">
                                        <input type="checkbox" class="custom-control-input" name="not_available" id="not_available" value="1" {{ $pet->not_available ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="not_available">{{ trans('cruds.pet.fields.not_available') }}</label>
                                    </div>
                                    @if($errors->has('not_available'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('not_available') }}
                                        </div>
                                    @endif
                                </div>
                                <div id="availabilityDates" class="form-group" style="display: none;">
                                    @if($errors->has('availability'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('availability') }}
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="required" for="from">Available From</label>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input class="form-control date" type="text" name="from" id="from" value="{{ old('from', $pet->from ? date('Y-m-d', strtotime($pet->from)) : '') }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control" type="time" name="from_time" id="from_time" value="{{ old('from_time', $pet->from_time ? date('H:i', strtotime($pet->from_time)) : '') }}">
                                                </div>
                                            </div>
                                            @if($errors->has('from'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('from') }}
                                                </div>
                                            @endif
                                            @if($errors->has('from_time'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('from_time') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="required" for="to">Available To</label>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input class="form-control date" type="text" name="to" id="to" value="{{ old('to', $pet->to ? date('Y-m-d', strtotime($pet->to)) : '') }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control" type="time" name="to_time" id="to_time" value="{{ old('to_time', $pet->to_time ? date('H:i', strtotime($pet->to_time)) : '') }}">
                                                </div>
                                            </div>
                                            @if($errors->has('to'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('to') }}
                                                </div>
                                            @endif
                                            @if($errors->has('to_time'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('to_time') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
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
@parent
<script>
    // Disable auto-discover before any Dropzone code runs
    if (typeof Dropzone !== 'undefined') {
        Dropzone.autoDiscover = false;
    }

    var uploadedPhotoMap = {}

    // Initialize Dropzone
    $(document).ready(function() {
        // Add form submission logging
        $('form').on('submit', function(e) {
            console.log('Form submitting to:', this.action);
            console.log('Form method:', this.method);
            console.log('Form data:', new FormData(this));
        });

        var myDropzone = new Dropzone("#photo-dropzone", {
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
        });
    });

    // Add this new script for handling availability dates
    $(document).ready(function() {
        function toggleAvailabilityDates() {
            if ($('#not_available').is(':checked')) {
                $('#availabilityDates').hide();
                $('#from, #to, #from_time, #to_time').removeAttr('required');
            } else {
                $('#availabilityDates').show();
                $('#from, #to, #from_time, #to_time').attr('required', 'required');
            }
        }

        // Initialize date pickers
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(),
            autoclose: true
        });

        // Set initial state
        toggleAvailabilityDates();

        // Handle checkbox change
        $('#not_available').change(function() {
            toggleAvailabilityDates();
        });

        // Form validation before submit
        $('form').submit(function(e) {
            console.log('Form validation started');
            console.log('not_available checked:', $('#not_available').is(':checked'));
            
            if (!$('#not_available').is(':checked')) {
                var from = $('#from').val();
                var to = $('#to').val();
                var fromTime = $('#from_time').val();
                var toTime = $('#to_time').val();

                console.log('Availability values:', {
                    from: from,
                    to: to,
                    fromTime: fromTime,
                    toTime: toTime
                });

                if (!from || !to || !fromTime || !toTime) {
                    console.log('Validation failed: Missing required fields');
                    e.preventDefault();
                    alert('Please fill in all availability dates and times when the pet is available.');
                    return false;
                }

                var fromDate = new Date(from + ' ' + fromTime);
                var toDate = new Date(to + ' ' + toTime);

                console.log('Date objects:', {
                    fromDate: fromDate,
                    toDate: toDate
                });

                if (fromDate >= toDate) {
                    console.log('Validation failed: End date before start date');
                    e.preventDefault();
                    alert('End date and time must be after start date and time.');
                    return false;
                }

                if (fromDate < new Date()) {
                    console.log('Validation failed: Start date in past');
                    e.preventDefault();
                    alert('Start date and time must be in the future.');
                    return false;
                }

                // Check if duration is within limits (24 hours)
                var durationHours = (toDate - fromDate) / (1000 * 60 * 60);
                console.log('Duration in hours:', durationHours);
                
                if (durationHours > 24) {
                    console.log('Validation failed: Duration > 24 hours');
                    e.preventDefault();
                    alert('Availability period cannot exceed 24 hours.');
                    return false;
                }

                // Check if time is within business hours (9 AM to 5 PM)
                var fromHour = fromDate.getHours();
                var toHour = toDate.getHours();
                console.log('Hours:', {
                    fromHour: fromHour,
                    toHour: toHour
                });
                
                if (fromHour < 9 || toHour > 17) {
                    console.log('Validation failed: Outside business hours');
                    e.preventDefault();
                    alert('Availability must be between 9 AM and 5 PM.');
                    return false;
                }
            }
            
            console.log('Form validation passed, proceeding with submission');
        });
    });
</script>
@endsection