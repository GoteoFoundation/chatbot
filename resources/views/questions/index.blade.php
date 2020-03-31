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
    @if(session()->get('warning'))
      <div class="alert alert-warning">
        {{ session()->get('warning') }}
      </div>
    @endif
  </div>

  <div class="col-sm-12">
    <a class="back-link" href="{{ route('topics.index') }}">
      <i class="fas fa-fw fa-arrow-left"></i>
      <span>@lang('Back to topics')</span>
    </a>

    <h1 class="display-3">@lang('Questions of topic') '<em>{{ $topic->name }}</em>'</h1>

    <div class="clearfix">

      @if($topic->questions->isEmpty())
        <button type="button" class="btn btn-primary float-right ml-3" data-toggle="modal" data-target="#copyNewQuestionModal" title="@lang('Copy Question From Another Topic')"><i class="far fa-copy"></i></button>
      @endif

      <a href="{{ route('topics.questions.create', $topic->id) }}" class="btn btn-primary float-right">@lang('Add Question')</a>

    </div>

    <ul class="nav nav-tabs my-4">
      <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() == 'topics.questions.index' ? 'active' : '' }}" href="{{ route('topics.questions.index', $topic->id) }}">
          <i class="fas fa-fw fa-list mr-1"></i> @lang('List')
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $topic->questions->isEmpty() ? 'disabled' : '' }} {{ Route::currentRouteName() == 'topics.questions.flow' ? 'active' : '' }}" href="{{ route('topics.questions.flow', $topic->id) }}">
          <i class="fas fa-fw fa-sitemap mr-1"></i> @lang('Flow')
        </a>
      </li>
    </ul>

    @yield('questions_mode')
  <div>

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
            @csrf
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
            @lang('Copy')
            <div class="spinner-border spinner-border-sm text-light ml-1" role="status">
              <span class="sr-only">@lang('Loading...')</span>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>

@endsection
