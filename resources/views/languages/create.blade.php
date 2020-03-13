@extends('layouts.backoffice')

@section('content')
  <div class="col-sm-8 offset-sm-2 mb-4">
    <a class="back-link" href="{{ route('languages.index') }}">
      <i class="fas fa-fw fa-arrow-left"></i>
      <span>@lang('Back to languages')</span>
    </a>
    <h1 class="display-3">@lang('Add language')</h1>
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
      <form method="post" action="{{ route('languages.store') }}">
        @csrf
        <div class="form-group">
          <label for="name">@lang('Name') *</label>
          <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
          <label for="iso_code">@lang('ISO 639-1 code') *</label>
          <input type="text" class="form-control w-25" id="iso_code" name="iso_code" value="{{ old('iso_code') }}" maxlength="2" minlength="2" required>
        </div>
        <div class="form-group">
          <label for="support_language">@lang('Support language') @lang('(optional)')</label>
          <select class="form-control select2-languages" id="support_language" name="support_language" style="width: 100%;"></select>
        </div>

        @unless($copies->isEmpty())
          <div class="border-top my-3"></div>
          <h2 class="display-6 mb-4">@lang('UI Copies')</h2>

          @foreach($copies as $key => $value)
            <div class="form-group">
              <label for="{{ $key }}">{{ $value }} *</label>
              <input type="text" class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ old($key) }}" required>
            </div>
          @endforeach
        @endunless

        <button type="submit" class="btn btn-primary">@lang('Save')</button>
      </form>
    </div>
  </div>
@endsection

@push('js')
  <script src="{{ asset('js/languages_create-edit.js') }}"></script>
  <script>
    goteoData = {
      languageIndexEndpoint: "{{ route('languages.index') }}",
      selectedOption: "{{ old('support_language') }}",
      translations: {
        languageSelectPlaceholder: "@lang('Select a language')"
      }
    };
  </script>
@endpush
