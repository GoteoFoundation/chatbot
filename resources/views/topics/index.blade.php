@extends('layouts.backoffice')

@section('content')
  <div class="col-sm-12">
    @if(session()->get('success'))
      <div class="alert alert-success">
        {{ session()->get('success') }}
      </div>
    @endif
    @if(session()->get('error'))
      <div class="alert alert-danger">
        {{ session()->get('error') }}
      </div>
    @endif
  </div>

  <div class="col-sm-12">
    <h1 class="display-3">@lang('Topics')</h1>

    <div class="my-4 clearfix">
      <a href="{{ route('topics.create') }}" class="btn btn-primary float-right">@lang('Add Topic')</a>
    </div>

    <table class="table table-striped" id="topics">
      <thead>
      <tr>
        <td>@lang('ID')</td>
        <td>@lang('Name')</td>
        <td></td>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  <div>

  <div class="modal fade" id="confirmDeletion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <h5>@lang('Are you sure you want to delete this topic?')</h5>
          <p>@lang('All its questions will be deleted too.')</p>
        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn">@lang('Cancel')</button>
          <button type="button" class="btn btn-primary accept">@lang('Delete')</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script src="{{ asset('js/topics_index.js') }}"></script>
  <script>
    goteoData = {
      datatableEndpoint: "{{ route('topics.datatable') }}",
      translations: {
        zeroRecords: "@lang('No matching records found')",
        info: "@lang('Page _PAGE_ of _PAGES_')",
        infoFiltered: "@lang('(filtered from _MAX_ total items)')",
        loadingRecords: "@lang('Loading...')",
        processing: "@lang('Loading...')",
        lengthMenu: "@lang('Items per page _MENU_')",
        search: "@lang('Search')",
        paginate: {
            first: "@lang('First')",
            last: "@lang('Last')",
            next: "@lang('Next')",
            previous: "@lang('Previous')"
        }
      }
    };
  </script>
@endpush
