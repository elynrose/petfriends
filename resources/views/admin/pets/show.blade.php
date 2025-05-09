@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.pet.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.id') }}
                        </th>
                        <td>
                            {{ $pet->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.photo') }}
                        </th>
                        <td>
                            @foreach($pet->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Pet::TYPE_SELECT[$pet->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.name') }}
                        </th>
                        <td>
                            {{ $pet->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.age') }}
                        </th>
                        <td>
                            {{ $pet->age }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.gender') }}
                        </th>
                        <td>
                            {{ App\Models\Pet::GENDER_SELECT[$pet->gender] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.notes') }}
                        </th>
                        <td>
                            {{ $pet->notes }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.not_available') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $pet->not_available ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.from') }}
                        </th>
                        <td>
                            {{ $pet->from }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.from_time') }}
                        </th>
                        <td>
                            {{ $pet->from_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.to') }}
                        </th>
                        <td>
                            {{ $pet->to }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pet.fields.to_time') }}
                        </th>
                        <td>
                            {{ $pet->to_time }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection