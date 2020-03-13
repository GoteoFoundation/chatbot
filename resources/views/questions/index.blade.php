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
@endsection
