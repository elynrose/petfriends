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
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class=" card col-md-12">
        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>{{ $pets->count() }} pet(s) found</h5>
                           <a href="{{ route('frontend.pets.create') }}" class="btn btn-success mt-3">{{ trans('global.add') }} {{ trans('cruds.pet.title_singular') }}</a>
                       
                </div>

            <div class="row">
                @if($pets->count() > 0)
                @foreach($pets as $pet)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            
                            <div class="card-img-top" style="position:relative;">
                            @if($pet->user_id === Auth::user()->id)
                                            @can('pet_delete')
                                            <form action="{{ route('frontend.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="position:absolute; top:10px; right:10px; z-index: 11;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-xs btn-default" value="{{ trans('global.delete') }}">
                                                   <i class="fa fa-trash"></i>
                                                </button>
                                                </form>
                                            @endcan
                                        @endif

                                        @if($pet->bookings_count > 0)
                                            <div class="notification-badge" title="Pending Requests">
                                               <a href="{{ route('frontend.requests.index') }}" class="text-white"> {{ $pet->bookings_count }}</a>
                                            </div>
                                        @endif
                                    
                                @foreach($pet->photo as $media)
                                    <img src="{{ $media->getUrl() }}" class="img-fluid" alt="{{ $pet->name }}">
                                @endforeach
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $pet->name }}  
                                  @if($pet->user_id === Auth::user()->id) 
                                    @can('pet_edit')
                                            <a class="btn btn-xs btn-default text-dark" href="{{ route('frontend.pets.edit', $pet->id) }}" ><i class="fa fa-pencil"></i></a>
                                        @endcan
                                    @endif
                                    </h5>
                                <p class="card-text">
                                    <strong>{{ trans('cruds.pet.fields.type') }}:</strong> {{ App\Models\Pet::TYPE_SELECT[$pet->type] ?? '' }}<br>
                                    <strong>{{ trans('cruds.pet.fields.age') }}:</strong> {{ $pet->age ?? '' }}<br>
                                    <strong>{{ trans('cruds.pet.fields.gender') }}:</strong> {{ App\Models\Pet::GENDER_SELECT[$pet->gender] ?? '' }}<br>
                                    <strong>@if($pet->not_available==true)<i class="fa fa-stop-circle text-danger"></i> {{ trans('cruds.pet.fields.not_available') }} @else <i class="fa fa-check-circle text-success"></i> {{ trans('cruds.pet.fields.available') }} @endif</strong> 
                                    @if(!$pet->not_available)
                                        @php
                                            $creditService = app(App\Services\CreditService::class);
                                            $hours = $creditService->calculatePetAvailabilityHours($pet);
                                        @endphp
                                        <br><strong>Credits to use:</strong> -{{ $hours }} credits
                                    @endif
                                </p>
                                <div class="btn-group">



                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @else
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert">
                            {{ trans('global.no_pets_found') }}
                        </div>
                    </div>
                @endif
            </div>
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
                                    @foreach(App\Models\Pet::TYPE_SELECT as $key => $label)
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
                                    <input type="hidden" class="custom-control-input" name="not_available" id="not_available" value="1">
                                </div>
                                @if($errors->has('not_available'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('not_available') }}
                                    </div>
                                @endif
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
</script>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .notification-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.5em 0.8em;
        font-size: 1em;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
        min-width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection