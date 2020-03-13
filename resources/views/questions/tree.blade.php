@extends('questions.index')

@section('questions_mode')
  <div id="topic_tree"></div>
@endsection

@push('js')
  <script src="{{ asset('js/questions_tree.js') }}"></script>
  <script>
    goteoData = {tree: {!! json_encode($tree) !!}};
  </script>
@endpush
