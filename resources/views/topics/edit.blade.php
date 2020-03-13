@extends('layouts.backoffice')

@section('content')
  <div>
    <div class="col-sm-8 offset-sm-2">
      <a class="back-link" href="{{ route('topics.index') }}">
        <i class="fas fa-fw fa-arrow-left"></i>
        <span>@lang('Back to topics')</span>
      </a>
      <h1 class="display-3">@lang('Edit topic') {{ $topic->id }}</h1>
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
        <form method="post" action="{{ route('topics.update', $topic->id) }}">
          @method('PATCH')
          @csrf
          <div class="form-group">
            <label for="name">@lang('Name') *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $topic->name }}" required>
          </div>
          <button type="submit" class="btn btn-primary">@lang('Save changes')</button>
        </form>
      </div>
    </div>
  </div>
@endsection
