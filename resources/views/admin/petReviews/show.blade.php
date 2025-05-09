@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.petReview.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pet-reviews.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.petReview.fields.score') }}
                        </th>
                        <td>
                            {{ $petReview->score }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.petReview.fields.comment') }}
                        </th>
                        <td>
                            {{ $petReview->comment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.petReview.fields.pet') }}
                        </th>
                        <td>
                            {{ $petReview->pet->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.petReview.fields.booking') }}
                        </th>
                        <td>
                            {{ $petReview->booking->status ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pet-reviews.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection