@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.support.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.supports.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.support.fields.name') }}
                        </th>
                        <td>
                            {{ $support->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.support.fields.email') }}
                        </th>
                        <td>
                            {{ $support->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.support.fields.phone') }}
                        </th>
                        <td>
                            {{ $support->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.support.fields.photo') }}
                        </th>
                        <td>
                            @foreach($support->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.support.fields.message') }}
                        </th>
                        <td>
                            {!! $support->message !!}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.supports.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection