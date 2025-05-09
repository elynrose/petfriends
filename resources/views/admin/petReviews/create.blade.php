@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.petReview.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.pet-reviews.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="score">{{ trans('cruds.petReview.fields.score') }}</label>
                <input class="form-control {{ $errors->has('score') ? 'is-invalid' : '' }}" type="number" name="score" id="score" value="{{ old('score', '') }}" step="1" required>
                @if($errors->has('score'))
                    <div class="invalid-feedback">
                        {{ $errors->first('score') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.petReview.fields.score_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="comment">{{ trans('cruds.petReview.fields.comment') }}</label>
                <textarea class="form-control {{ $errors->has('comment') ? 'is-invalid' : '' }}" name="comment" id="comment">{{ old('comment') }}</textarea>
                @if($errors->has('comment'))
                    <div class="invalid-feedback">
                        {{ $errors->first('comment') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.petReview.fields.comment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pet_id">{{ trans('cruds.petReview.fields.pet') }}</label>
                <select class="form-control select2 {{ $errors->has('pet') ? 'is-invalid' : '' }}" name="pet_id" id="pet_id" required>
                    @foreach($pets as $id => $entry)
                        <option value="{{ $id }}" {{ old('pet_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('pet'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pet') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.petReview.fields.pet_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="booking_id">{{ trans('cruds.petReview.fields.booking') }}</label>
                <select class="form-control select2 {{ $errors->has('booking') ? 'is-invalid' : '' }}" name="booking_id" id="booking_id" required>
                    @foreach($bookings as $id => $entry)
                        <option value="{{ $id }}" {{ old('booking_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('booking'))
                    <div class="invalid-feedback">
                        {{ $errors->first('booking') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.petReview.fields.booking_helper') }}</span>
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