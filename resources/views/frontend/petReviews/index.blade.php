@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('pet_review_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.pet-reviews.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.petReview.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.petReview.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-PetReview">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.petReview.fields.score') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.petReview.fields.comment') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.petReview.fields.pet') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.petReview.fields.booking') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($petReviews as $key => $petReview)
                                    <tr data-entry-id="{{ $petReview->id }}">
                                        <td>
                                            {{ $petReview->score ?? '' }}
                                        </td>
                                        <td>
                                            {{ $petReview->comment ?? '' }}
                                        </td>
                                        <td>
                                            {{ $petReview->pet->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $petReview->booking->status ?? '' }}
                                        </td>
                                        <td>
                                            @can('pet_review_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.pet-reviews.show', $petReview->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('pet_review_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.pet-reviews.edit', $petReview->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('pet_review_delete')
                                                <form action="{{ route('frontend.pet-reviews.destroy', $petReview->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('pet_review_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.pet-reviews.massDestroy') }}",
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
  let table = $('.datatable-PetReview:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection