@extends('questions.index')

@section('questions_mode')
  <div id="questions_filter" class="my-4" style="display:none;">
    <label class="ml-4">
      @lang('Filter by')
      <select id="questions_filter_select" class="custom-select custom-select-sm form-control form-control-sm">
        <option value="all">@lang('All')</option>
        <option value="incomplete">@lang('Incomplete')</option>
        <option value="not_reachable">@lang('Not Visible in Flow')</option>
        <option value="pending_data">@lang('Pending Translations')</option>
      </select>
    </label>
  </div>

  <table class="table table-striped" id="questions">
    <thead>
    <tr>
      <td>@lang('ID')</td>
      <td>@lang('Question')</td>
      <td></td>
    </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

  <div class="modal fade" id="confirmFirst" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <h5>@lang('Are you sure you want to set this question as the first one of this topic flow?')</h5>
          <p>@lang('This will become the first one seen by users after opening the help widget.')</p>
        </div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="btn">@lang('Cancel')</button>
          <button type="button" class="btn btn-primary accept">@lang('Set as First')</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmDeletion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <h5>@lang('Are you sure you want to delete this question?')</h5>
          <p>@lang('It may have linked questions that would become invisible in the flow.')</p>
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
  <script src="{{ asset('js/questions_list.js') }}"></script>
  <script>
    goteoData = {
      datatableEndpoint: "{{ route('topics.questions.datatable', $topic->id) }}",
      questionIndexEndpoint: "{{ route('topics.questions.index', $topic->id) }}",
      questionStoreEndpoint: "{{ route('topics.questions.store', $topic->id) }}",
      questionCopyEndpoint: "{{ route('topics.questions.copy', $topic->id) }}",
      topicsEndpoint: "{{ route('topics.index') }}",
      topicIndexEndpoint: "{{ route('topics.questions.index', $topic->id) }}",
      topicId: "{{ $topic->id }}",
      translations: {
        zeroRecords: "@lang('No matching records found')",
        info: "@lang('Page _PAGE_ of _PAGES_')",
        infoFiltered: "@lang('(filtered from _MAX_ total items)')",
        loadingRecords: "@lang('Loading...')",
        processing: "@lang('Loading...')",
        lengthMenu: "@lang('Items per page _MENU_')",
        search: "@lang('Search')",
        searchPlaceholder: "@lang('Questions & answers…')",
        paginate: {
            first: "@lang('First')",
            last: "@lang('Last')",
            next: "@lang('Next')",
            previous: "@lang('Previous')"
        },
        topicSelectPlaceholder: "@lang('Select a topic')",
        questionSelectPlaceholder: "@lang('Select a question')"
      }
    };
  </script>
@endpush
