@extends('layouts.frontend')

@section('content')
<style>
    .card-img-top {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    .card-img-top img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    .pet-rating {
        font-size: 1.1em;
    }
    .pet-rating i {
        margin-right: 2px;
    }
    .pet-rating span {
        color: #6c757d;
        font-size: 0.9em;
    }
</style>
<div class="container">
    <div class="row">
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <!-- Search Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('frontend.home') }}" class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">{{ trans('cruds.pet.fields.type') }}</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                                            @foreach($petTypes as $key => $label)
                                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="zip_code" class="font-weight-bold">Zip Code</label>
                                        <input type="text" class="form-control" name="zip_code" id="zip_code" value="{{ request('zip_code') }}" placeholder="Enter zip code">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from" class="font-weight-bold">From Date</label>
                                        <input type="text" class="form-control date" name="date_from" id="date_from" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to" class="font-weight-bold">To Date</label>
                                        <input type="text" class="form-control date" name="date_to" id="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('frontend.home') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <h2 class="mb-4">Available Pets</h2>


                    <div class="row">
                        @if($pets->count() > 0)
                            @foreach($pets as $pet)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-img-top" style="position: relative;">
                                            @foreach($pet->photo as $media)
                                                <img src="{{ $media->getUrl() }}" class="img-fluid" alt="{{ $pet->name }}">
                                                @if($pet->user->photo)
                                                <img src="{{ $pet->user->photo->getUrl() }}" class="img-fluid rounded-circle" style="width: 50px; height: 50px; position: absolute; bottom: 10px; right: 10px;">
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $pet->name }} <span class="badge badge-pill badge-default"><strong>@if($pet->not_available==true)<i class="fa fa-stop-circle text-danger"> </i> {{ trans('cruds.pet.fields.not_available') }} @else <i class="fa fa-check-circle text-success"></i> {{ trans('cruds.pet.fields.available') }} @endif </strong> 
                                            </span>
                                            </h5>
                                            @php
                                                $averageRating = $pet->petReviews ? $pet->petReviews->avg('score') : 0;
                                                $fullStars = floor($averageRating);
                                                $halfStar = $averageRating - $fullStars >= 0.5;
                                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                            @endphp
                                            <div class="pet-rating mb-2">
                                                @if($pet->petReviews && $pet->petReviews->count() > 0)
                                                    @for($i = 0; $i < $fullStars; $i++)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @endfor
                                                    @if($halfStar)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @endif
                                                    @for($i = 0; $i < $emptyStars; $i++)
                                                        <i class="far fa-star text-warning"></i>
                                                    @endfor
                                                    <span class="ml-1">({{ number_format($averageRating, 1) }})</span>
                                                @else
                                                    <span class="text-muted">No reviews yet</span>
                                                @endif
                                            </div>
                                            <p class="card-text">

                                                <strong>{{ trans('cruds.pet.fields.gender') }}:</strong> {{ App\Models\Pet::GENDER_SELECT[$pet->gender] ?? '' }}<br>
                                                <strong>Location:</strong> {{ $pet->user->zip_code ?? 'N/A' }}<br>
                                                @if($pet->from || $pet->to)
                                                    <strong>Available:</strong> 
                                                    {{ $pet->from ? date('M d, Y', strtotime($pet->from)) : 'Any time' }} 
                                                    to 
                                                    {{ $pet->to ? date('M d, Y', strtotime($pet->to)) : 'Any time' }}
                                                @endif
                                            <br>
                                                @php
                                                    $creditService = app(App\Services\CreditService::class);
                                                @endphp
                                                <strong>Credits:</strong> {{ $creditService->calculatePetAvailabilityHours($pet) }} credits
                                            </p>
                                            <div class="btn-group">
                                                @can('pet_show')
                                                    <a class="btn btn-sm btn-primary" href="{{ route('frontend.pets.show', $pet->id) }}">
                                                        {{ trans('global.moreinfo') }}
                                                    </a>
                                                @endcan

                                                @if($pet->user_id === Auth::user()->id)
                                                @can('pet_edit')
                                                    <a class="btn btn-xs btn-info" href="{{ route('frontend.pets.edit', $pet->id) }}">
                                                       <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endcan
                                               

                                                @can('pet_delete')
                                                    <form action="{{ route('frontend.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                           <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    No available pets found matching your search criteria.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="col-md-12">
            <h2 class="mb-4">Featured Pets</h2>
            @if($featuredPets->isEmpty())
                <div class="alert alert-info">
                    No featured pets at the moment.
                </div>
            @else
                <div class="row">
                    @foreach($featuredPets as $pet)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($pet->photo && $pet->photo->first())
                                    <img src="{{ $pet->photo->first()->getUrl() }}" class="card-img-top" alt="{{ $pet->name }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {{ $pet->name }}
                                        
                                    </h5>
                                    <p class="card-text">
                                        <strong>Type:</strong> {{ $pet->type }}<br>
                                        <strong>Age:</strong> {{ $pet->age }} years<br>
                                        <strong>Gender:</strong> {{ $pet->gender }}<br>
                                        <strong>Featured Until:</strong> {{ $pet->featured_until->format('g:i A') }}
                                    </p>
                                    <a href="{{ route('frontend.pets.show', $pet->id) }}" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

  
</div>

<!-- Create Pet Modal -->
<div class="modal fade" id="createPetModal" tabindex="-1" role="dialog" aria-labelledby="createPetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPetModalLabel">{{ trans('global.create') }} {{ trans('cruds.pet.title_singular') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('frontend.pets.store') }}" enctype="multipart/form-data">
                    @method('POST')
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
                                <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
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
                                    @foreach($petTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                <input class="form-control" type="number" name="age" id="age" value="{{ old('age', '') }}" step="1" required>
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
                                        <option value="{{ $key }}" {{ old('gender', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                <textarea class="form-control" name="notes" id="notes" rows="2">{{ old('notes', '') }}</textarea>
                                @if($errors->has('notes'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('notes') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="not_available" value="0">
                                    <input type="checkbox" class="custom-control-input" name="not_available" id="not_available" value="1" {{ old('not_available', 0) == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="not_available">{{ trans('cruds.pet.fields.not_available') }}</label>
                                </div>
                                @if($errors->has('not_available'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('not_available') }}
                                    </div>
                                @endif
                            </div>
                            <div id="availabilityDates" class="form-group" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="required" for="from">Available From</label>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="from" id="from" value="{{ old('from', '') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" type="time" name="from_time" id="from_time" value="{{ old('from_time', '') }}">
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
                                                <input class="form-control" type="date" name="to" id="to" value="{{ old('to', '') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" type="time" name="to_time" id="to_time" value="{{ old('to_time', '') }}">
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
                    <div class="modal-footer">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">{{ trans('global.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
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

        // Initial state
        toggleAvailabilityDates();

        // Toggle on checkbox change
        $('#not_available').change(function() {
            toggleAvailabilityDates();
        });
    });
</script>
@endsection