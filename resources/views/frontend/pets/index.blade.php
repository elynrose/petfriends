@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('pet_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.pets.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.pet.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.pet.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Pet">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.pet.fields.photo') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.type') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.name') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.age') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.gender') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.not_available') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.from') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.from_time') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.to') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.pet.fields.to_time') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pets as $key => $pet)
                                    <tr data-entry-id="{{ $pet->id }}">
                                        <td>
                                            @foreach($pet->photo as $key => $media)
                                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                                    <img src="{{ $media->getUrl('thumb') }}">
                                                </a>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{ App\Models\Pet::TYPE_SELECT[$pet->type] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $pet->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $pet->age ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Pet::GENDER_SELECT[$pet->gender] ?? '' }}
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $pet->not_available ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $pet->not_available ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $pet->from ?? '' }}
                                        </td>
                                        <td>
                                            {{ $pet->from_time ?? '' }}
                                        </td>
                                        <td>
                                            {{ $pet->to ?? '' }}
                                        </td>
                                        <td>
                                            {{ $pet->to_time ?? '' }}
                                        </td>
                                        <td>
                                            @can('pet_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.pets.show', $pet->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('pet_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.pets.edit', $pet->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('pet_delete')
                                                <form action="{{ route('frontend.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('pet_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.pets.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Pet:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection