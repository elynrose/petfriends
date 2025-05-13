@extends('layouts.frontend')
@section('content')

<div class="container py-4">
    <div class="row">
        @if(session('success'))
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ trans('global.my_profile') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if($user->getFirstMedia('photo'))
                                <img src="{{ $user->getFirstMedia('photo')->getUrl() }}" 
                                     alt="{{ $user->name }}" 
                                     class="img-fluid rounded-circle shadow-sm" 
                                     style="max-width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default-avatar.svg') }}" 
                                     alt="{{ $user->name }}" 
                                     class="img-fluid rounded-circle shadow-sm" 
                                     style="max-width: 150px; height: 150px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            @if($user->phone)
                                <p class="mb-0"><i class="fas fa-phone text-primary"></i> {{ $user->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('frontend.profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="name">{{ trans('cruds.user.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                                   type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email">{{ trans('cruds.user.fields.email') }}</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                   type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="required" for="street_address">Street Address</label>
                            <input class="form-control {{ $errors->has('street_address') ? 'is-invalid' : '' }}" 
                                   type="text" 
                                   name="street_address" 
                                   id="street_address" 
                                   value="{{ old('street_address', auth()->user()->street_address) }}" 
                                   required>
                            @if($errors->has('street_address'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('street_address') }}
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="city">City</label>
                                    <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" 
                                           type="text" 
                                           name="city" 
                                           id="city" 
                                           value="{{ old('city', auth()->user()->city) }}" 
                                           required>
                                    @if($errors->has('city'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('city') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="state">State</label>
                                    <input class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}" 
                                           type="text" 
                                           name="state" 
                                           id="state" 
                                           value="{{ old('state', auth()->user()->state) }}" 
                                           required>
                                    @if($errors->has('state'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('state') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="required" for="zip_code">Zip Code</label>
                            <input class="form-control {{ $errors->has('zip_code') ? 'is-invalid' : '' }}" 
                                   type="text" 
                                   name="zip_code" 
                                   id="zip_code" 
                                   value="{{ old('zip_code', auth()->user()->zip_code) }}" 
                                   required>
                            @if($errors->has('zip_code'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('zip_code') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="phone">{{ trans('cruds.user.fields.phone') }}</label>
                            <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                                   type="text" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone', $user->phone) }}">
                            @if($errors->has('phone'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="sms_notifications" 
                                       name="sms_notifications" 
                                       value="1" 
                                       {{ old('sms_notifications', $user->sms_notifications) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="sms_notifications">
                                    {{ trans('cruds.user.fields.sms_notifications') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ trans('cruds.user.fields.sms_notifications_helper') }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="photo">{{ trans('cruds.user.fields.photo') }}</label>
                            <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" 
                                 id="photo-dropzone">
                            </div>
                            @if($errors->has('photo'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('photo') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save mr-1"></i> {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ trans('global.change_password') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('frontend.profile.password') }}">
                        @csrf
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="required" for="password">New {{ trans('cruds.user.fields.password') }}</label>
                            <input class="form-control" 
                                   type="password" 
                                   name="password" 
                                   id="password" 
                                   required>
                            @if($errors->has('password'))
                                <span class="help-block text-danger" role="alert">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="required" for="password_confirmation">
                                Repeat New {{ trans('cruds.user.fields.password') }}
                            </label>
                            <input class="form-control" 
                                   type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-key mr-1"></i> {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ trans('global.delete_account') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Once you delete your account, there is no going back. Please be certain.
                    </p>
                    <form method="POST" 
                          action="{{ route('frontend.profile.destroy') }}" 
                          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        @csrf
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                <i class="fas fa-trash-alt mr-1"></i> {{ trans('global.delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(Route::has('frontend.profile.toggle-two-factor'))
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ trans('global.two_factor.title') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('frontend.profile.toggle-two-factor') }}">
                            @csrf
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    {{ auth()->user()->two_factor ? trans('global.two_factor.disable') : trans('global.two_factor.enable') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
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