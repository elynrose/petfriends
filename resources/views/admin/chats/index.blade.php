@extends('layouts.admin')
@section('content')
@can('chat_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.chats.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.chat.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.chat.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Chat">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.chat.fields.booking') }}
                        </th>
                        <th>
                            {{ trans('cruds.chat.fields.from') }}
                        </th>
                        <th>
                            {{ trans('cruds.chat.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.chat.fields.read') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chats as $key => $chat)
                        <tr data-entry-id="{{ $chat->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $chat->booking->status ?? '' }}
                            </td>
                            <td>
                                {{ $chat->from->name ?? '' }}
                            </td>
                            <td>
                                @if($chat->photo)
                                    <a href="{{ $chat->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $chat->photo->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span style="display:none">{{ $chat->read ?? '' }}</span>
                                <input type="checkbox" disabled="disabled" {{ $chat->read ? 'checked' : '' }}>
                            </td>
                            <td>
                                @can('chat_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.chats.show', $chat->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('chat_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.chats.edit', $chat->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('chat_delete')
                                    <form action="{{ route('admin.chats.destroy', $chat->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('chat_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.chats.massDestroy') }}",
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
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Chat:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection