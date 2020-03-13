@extends('layouts.backoffice')

@php
  $itemsOnPage = 0;
@endphp

@section('content')
<div class="py-4">
  <div class="col-sm-8 offset-sm-2">
    <a class="back-link" href="{{ route('topics.questions.index', [$topic->id]) }}">
      <i class="fas fa-fw fa-arrow-left"></i>
      <span>@lang('Back to questions of topic') '{{ $topic->name }}'</span>
    </a>
    <h1 class="display-3">@lang('Edit question') {{ $question->id }}</h1>
    <div>
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="editQuestionForm" method="post" action="{{ route('topics.questions.update', [$topic->id, $question->id]) }}">
        @method('PATCH')
        @csrf
        <div class="form-group">
          @php($actual_field = 'question')
          <label>@lang('Question text') *</label>
          <div class="tabs-langs">
            <ul class="nav nav-tabs" id="{{ $actual_field }}-tabs" role="tablist">
              @foreach($languages as $lang)
                <li class="nav-item">
                  <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="{{ $actual_field }}-tab-{{ $lang->iso_code }}" data-toggle="tab"
                     href="#{{ $actual_field }}-{{ $lang->iso_code }}" role="tab" aria-controls="{{ $actual_field }}-tab-{{ $lang->iso_code }}"
                     aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                    {{ $lang->iso_code }}{{ $lang->is_parent ? ' *' : '' }}
                  </a>
                </li>
              @endforeach
            </ul>
            <div class="tab-content" id="{{ $actual_field }}-tabs-content">
              @foreach($languages as $lang)
                <div class="tab-pane{{ $lang->is_parent ? ' show active' : '' }}" id="{{ $actual_field }}-{{ $lang->iso_code }}" role="tabpanel" aria-labelledby="{{ $actual_field }}-tab-{{ $lang->iso_code }}">
                  <input type="text" class="form-control" name="{{ $actual_field }}[{{ $lang->id }}]" value="{{ old($actual_field . '.' . $lang->id, $question->trans($lang->id, true)) }}" {{ $lang->is_parent ? 'required' : '' }}>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div id="answers" class="form-group">
          <!-- Repeater Items -->
          <div id="items" data-repeater-list="answers">
            <!-- Repeater Content -->
            <div class="card mb-4 order-bottom-primary" data-repeater-item data-repeater-delete-me-onload style="display:none;">

              <div class="card-body">
                <input type="hidden" name="answers[id][]" value="" />

                <div class="row">

                  <div class="col-1">
                    <i class="fas fa-grip-lines"></i>
                  </div>

                  <div class="col-9">
                    <div class="form-group">
                      <label>@lang('Answer text') *</label>

                      <div class="tabs-langs">
                        <ul class="nav nav-tabs" id="answers-tabs-id1" role="tablist">
                          @foreach($languages as $lang)
                            <li class="nav-item">
                              <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="answers-tab-{{ $lang->iso_code }}-id1" data-toggle="tab" href="#answers-{{ $lang->iso_code }}-id1" role="tab" aria-controls="answers-tab-{{ $lang->iso_code }}-id1" aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                                {{ $lang->iso_code }}{{ $lang->is_parent ? ' *' : '' }}
                              </a>
                            </li>
                          @endforeach
                        </ul>
                        <div class="tab-content" id="answers-tabs-tabs-content-id1">
                          @foreach($languages as $lang)
                            <div class="tab-pane{{ $lang->is_parent ? ' show active' : '' }}" id="answers-{{ $lang->iso_code }}-id1" role="tabpanel" aria-labelledby="answers-tab-{{ $lang->iso_code }}-id1">
                              <input type="text" class="form-control" name="answers[text-{{ $lang->id }}][]" value="{{ old($actual_field . '.' . $lang->iso_code) }}" {{ $lang->is_parent ? 'required' : '' }}>
                            </div>
                          @endforeach
                        </div>
                      </div>

                    </div>
                    <div class="form-group">
                      <label>@lang('Links to:') *</label>
                      <label class="radiobutton">
                        <input type="radio" class="answer_type" name="answers[type][]" value="question" checked>
                        @lang('Question')
                      </label>
                      <label class="radiobutton">
                        <input type="radio" class="answer_type" name="answers[type][]" value="url">
                        @lang('URL')
                      </label>
                    </div>
                    <div class="answers_group">
                      <div class="form-group answer_type_option_question">
                        <label for="answers[question][]">@lang('Linked question') *</label>
                        <div class="row">
                          <div class="col-8">
                            <select class="form-control select2-questions" name="answers[question][]" style="width: 100%;" required>
                              <option value=""></option>
                            </select>
                          </div>
                          <div class="col-4">
                            <!-- Button create question -->
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#createNewQuestionModal" title="@lang('Add Question')"><i class="fas fa-plus"></i></button>

                            <!-- Button copy question -->
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#copyNewQuestionModal" title="@lang('Copy Question From Another Topic')"><i class="far fa-copy"></i></button>
                          </div>
                        </div>
                      </div>
                      <div class="form-group answer_type_option_url" style="display: none;">
                        <label>@lang('Linked URL') *</label>

                        <div class="tabs-langs">
                          <ul class="nav nav-tabs" id="answers-tabs-type-id1" role="tablist">
                            @foreach($languages as $lang)
                              <li class="nav-item">
                                <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="answers-tab-type-{{ $lang->iso_code }}-id1" data-toggle="tab" href="#answers-type-{{ $lang->iso_code }}-id1" role="tab" aria-controls="answers-tab-type-{{ $lang->iso_code }}-id1" aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                                  {{ $lang->iso_code }}{{ $lang->is_parent ? ' *' : '' }}
                                </a>
                              </li>
                            @endforeach
                          </ul>
                          <div class="tab-content" id="answers-tabs-tabs-type-content-id1">
                            @foreach($languages as $lang)
                              <div class="tab-pane{{ $lang->is_parent ? ' show active' : '' }}" id="answers-type-{{ $lang->iso_code }}-id1" role="tabpanel" aria-labelledby="answers-tab-type-{{ $lang->iso_code }}-id1">
                                <input type="text" class="form-control" name="answers[url-{{ $lang->id }}][]" value="{{ old($actual_field . '.' . $lang->id) }}">
                              </div>
                            @endforeach
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-2">
                    <!-- Repeater Remove Btn -->
                    <div class="pull-right repeater-remove-btn float-right">
                      <button type="button" class="btn btn-sm btn-danger" data-repeater-delete title="@lang('Delete Answer')"><i class="far fa-trash-alt"></i></button>
                    </div>
                  </div>

                </div>

              </div>
            </div>

            @if(old('answers', $answers) && is_array(old('answers', $answers)))
              @foreach(old('answers', $answers) as $answer)

                @php($itemsOnPage++)

                <div class="card mb-4 order-bottom-primary" data-repeater-item>
                  <div class="card-body">
                    <input type="hidden" name="answers[id][]" value="{{ $answer['id'] }}" />

                    <div class="row">

                      <div class="col-1">
                        <i class="fas fa-grip-lines"></i>
                      </div>

                      <div class="col-9">
                        <div class="form-group">
                          <label>@lang('Answer text') *</label>

                          <div class="tabs-langs">
                            <ul class="nav nav-tabs" id="answers-tabs-id{{ $itemsOnPage }}" role="tablist">
                              @foreach($languages as $lang)
                                <li class="nav-item">
                                  <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="answers-tab-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" data-toggle="tab" href="#answers-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" role="tab" aria-controls="answers-tab-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                                    {{ $lang->iso_code }}{{ $lang->is_parent ? ' *' : '' }}
                                  </a>
                                </li>
                              @endforeach
                            </ul>
                            <div class="tab-content" id="answers-tabs-tabs-content-id{{ $itemsOnPage }}">
                              @foreach($languages as $lang)
                                <div class="tab-pane{{ $lang->is_parent ? ' show active' : '' }}" id="answers-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" role="tabpanel" aria-labelledby="answers-tab-{{ $lang->iso_code }}-id{{ $itemsOnPage }}">
                                  <input type="text" class="form-control" name="answers[text-{{ $lang->id }}][]" value="{{ $answer['text-' . $lang->id] }}" {{ $lang->is_parent ? 'required' : '' }}>
                                </div>
                              @endforeach
                            </div>
                          </div>

                        </div>
                        <div class="form-group">
                          <label>@lang('Links to:') *</label>
                          <label class="radiobutton">
                            <input type="radio" class="answer_type" name="answers[type][]" value="question" {{ $answer['type'] == 'question' ? 'checked' : '' }}>
                            @lang('Question')
                          </label>
                          <label class="radiobutton">
                            <input type="radio" class="answer_type" name="answers[type][]" value="url" {{ $answer['type'] == 'url' ? 'checked' : '' }}>
                            @lang('URL')
                          </label>
                        </div>
                        <div class="answers_group">
                          <div class="form-group answer_type_option_question" {!! $answer['type'] == 'question' ? '' : 'style="display: none;"' !!}>
                            <label for="answers[question][]">@lang('Linked question') *</label>
                            <div class="row">
                              <div class="col-8">
                                <select class="form-control select2-questions" name="answers[question][]" style="width: 100%;" {{ $answer['type'] == 'question' ? 'required' : '' }}>
                                  <option value=""></option>
                                  @foreach($questions as $questionSelect)
                                    <option value="{{ $questionSelect->id }}" {{ ($answer['question'] ?? '') == $questionSelect->id ? 'selected' : '' }}>{{ $questionSelect->question }}</option>
                                  @endforeach
                                </select>
                              </div>
                              <div class="col-4">
                                <!-- Button create question -->
                                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#createNewQuestionModal" title="@lang('Add Question')"><i class="fas fa-plus"></i></button>

                                <!-- Button copy question -->
                                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#copyNewQuestionModal" title="@lang('Copy Question From Another Topic')"><i class="far fa-copy"></i></button>
                              </div>
                            </div>
                          </div>
                          <div class="form-group answer_type_option_url" {!! $answer['type'] == 'url' ? '' : 'style="display: none;"' !!}>
                            <label>@lang('Linked URL') *</label>

                            <div class="tabs-langs">
                              <ul class="nav nav-tabs" id="answers-tabs-type-id{{ $itemsOnPage }}" role="tablist">
                                @foreach($languages as $lang)
                                  <li class="nav-item">
                                    <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="answers-tab-type-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" data-toggle="tab" href="#answers-type-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" role="tab" aria-controls="answers-tab-type-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                                      {{ $lang->iso_code }}{{ $lang->is_parent ? ' *' : '' }}
                                    </a>
                                  </li>
                                @endforeach
                              </ul>
                              <div class="tab-content" id="answers-tabs-tabs-type-content-id{{ $itemsOnPage }}">
                                @foreach($languages as $lang)
                                  <div class="tab-pane{{ $lang->is_parent ? ' show active' : '' }}" id="answers-type-{{ $lang->iso_code }}-id{{ $itemsOnPage }}" role="tabpanel" aria-labelledby="answers-tab-type-{{ $lang->iso_code }}-id{{ $itemsOnPage }}">
                                    <input type="text" class="form-control" name="answers[url-{{ $lang->id }}][]" value="{{ $answer['url-' . $lang->id] }}" {{ ($lang->is_parent && ($answer['type'] == 'url')) ? 'required' : '' }}>
                                  </div>
                                @endforeach
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>

                      <div class="col-2">
                        <!-- Repeater Remove Btn -->
                        <div class="pull-right repeater-remove-btn float-right">
                          <button type="button" class="btn btn-sm btn-danger" data-repeater-delete title="@lang('Delete Answer')"><i class="far fa-trash-alt"></i></button>
                        </div>
                      </div>

                    </div>

                  </div>
                </div>

              @endforeach
            @endif
          </div>

          <div class="repeater-heading clearfix">
            <button type="button" class="btn btn-secondary btn-sm float-right" data-repeater-create><i class="fas fa-plus"></i> @lang('Add Answer')</button>
          </div>

        </div>
        <button id="editQuestionFormButton" type="submit" class="btn btn-primary">@lang('Save changes')</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal create question -->
<div class="modal fade" id="createNewQuestionModal" tabindex="-1" role="dialog" aria-labelledby="createNewQuestionModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('Add question')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info" role="alert">
          @lang('You will be able to edit the details and translations of this question later.')
        </div>
        <form id="createNewQuestionForm" action="#">
          <div class="form-group">
            @php($actual_field = 'new_question')
            <label for="{{ $actual_field }}">@lang('Question text') *</label>
            <div class="tabs-langs">
              <ul class="nav nav-tabs" id="{{ $actual_field }}-tabs" role="tablist">
                @foreach($languages as $lang)
                  <li class="nav-item">
                    <a class="nav-link{{ $lang->is_parent ? ' active' : '' }}" id="{{ $actual_field }}-tab-{{ $lang->iso_code }}" data-toggle="tab"
                       href="#{{ $actual_field }}-{{ $lang->iso_code }}" role="tab" aria-controls="{{ $actual_field }}-tab-{{ $lang->iso_code }}"
                       aria-selected="{{ $lang->is_parent ? ' true' : 'false' }}">
                      {{ $lang->iso_code }} {{ $lang->is_parent ? ' *' : '' }}
                    </a>
                  </li>
                @endforeach
              </ul>
              <div class="tab-content" id="{{ $actual_field }}-tabs-content">
                @foreach($languages as $lang)
                  <div class="tab-pane fade{{ $lang->is_parent ? ' show active' : '' }}" id="{{ $actual_field }}-{{ $lang->iso_code }}"
                       role="tabpanel" aria-labelledby="{{ $actual_field }}-tab-{{ $lang->iso_code }}">
                    <input type="text" class="form-control" name="{{ $actual_field }}[{{ $lang->iso_code }}]" value="{{ old($actual_field . '.' . $lang->iso_code) }}" {{ $lang->is_parent ? 'required' : '' }}>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">@lang('Cancel')</button>
        <button type="submit" id="createNewQuestionFormButton" form="createNewQuestionForm" class="btn btn-primary">@lang('Save and Select')</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal copy question -->
<div class="modal fade" id="copyNewQuestionModal" tabindex="-1" role="dialog" aria-labelledby="copyNewQuestionModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('Copy question from another topic')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info" role="alert">
          @lang('The selected question and all its answers and related questions will be copied to the current topic flow.')
        </div>
        <form id="copyNewQuestionForm" action="#">
          <div class="form-group">
            <label for="copy_question_topic">@lang('Topic') *</label>
            <div>
              <select class="form-control select2-topics" id="copy_question_topic" name="copy_question_topic" style="width: 100%;" required></select>
            </div>
          </div>
          <div class="form-group">
            <label for="copy_question_question">@lang('Question to be copied') *</label>
            <div>
              <select class="form-control select2-copyquestion" id="copy_question_question" name="copy_question_question" style="width: 100%;" disabled required></select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">@lang('Cancel')</button>
        <button type="submit" id="copyNewQuestionFormButton" form="copyNewQuestionForm" class="btn btn-primary">
          @lang('Copy and Select')
          <div class="spinner-border spinner-border-sm text-light ml-1" role="status">
            <span class="sr-only">@lang('Loading...')</span>
          </div>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal dialog deletion confirmation -->
<div class="modal fade" id="confirmDeletion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h5>@lang('Are you sure you want to delete this answer?')</h5>
        <p>@lang('The actual deletion will be done after saving the question.')</p>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">@lang('Cancel')</button>
        <button type="button" data-dismiss="modal" class="btn btn-primary accept">@lang('Delete')</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js')
  <script src="{{ asset('js/questions_create-edit.js') }}"></script>
  <script>
    goteoData = {
      questionIndexEndpoint: "{{ route('topics.questions.index', $topic->id) }}",
      questionStoreEndpoint: "{{ route('topics.questions.store', $topic->id) }}",
      questionCopyEndpoint: "{{ route('topics.questions.copy', $topic->id) }}",
      topicsEndpoint: "{{ route('topics.index') }}",
      topicIndexEndpoint: "{{ route('topics.questions.index', $topic->id) }}",
      topicId: "{{ $topic->id }}",
      currentQuestionId: "{{ $question->id }}",
      isEmpty: {{ old('answers', $answers) && count(old('answers', $answers)) > 0 ? 'false' : 'true' }},
      initialRepeaterIndex: "{{ $itemsOnPage }}",
      translations: {
          topicSelectPlaceholder: "@lang('Select a topic')",
          questionSelectPlaceholder: "@lang('Select a question')"
      }
    };
  </script>
@endpush
