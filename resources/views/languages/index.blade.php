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
    <h1 class="display-3">@lang('Languages')</h1>

    <div class="my-4 clearfix">
      <a href="{{ route('languages.create') }}" class="btn btn-primary float-right">@lang('Add Language')</a>
    </div>

    <table class="table table-striped" id="languages">
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

  <div class="modal fade" id="confirmMain" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <h5>@lang('Are you sure you want to set this language as the main one?')</h5>
          <p>@lang('You will need to translate all questions and answers to at least this language.')</p>
        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn">@lang('Cancel')</button>
          <button type="button" class="btn btn-primary accept">@lang('Set as Main')</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmDeletion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <h5>@lang('Are you sure you want to delete this language?')</h5>
          <p>@lang('All questions and answers translations will be deleted too.')</p>
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
  <script src="{{ asset('js/languages_index.js') }}"></script>
  <script>
    goteoData = {
      datatableEndpoint: "{{ route('languages.datatable') }}",
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
