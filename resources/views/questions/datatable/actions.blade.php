<div class="btn-group actions-group float-right" role="group">
  @unless($question->is_parent)
    <form action="{{ route('topics.questions.first', [$topic->id, $question->id]) }}" method="post">
      @csrf
      @method('PATCH')
      <button type="submit" class="btn btn-outline-secondary mr-3" data-confirmation="confirmFirst">@lang('Set as First')</button>
    </form>
  @endunless
    <a class="btn btn-secondary" href="{{ route('topics.questions.edit', [$topic->id, $question->id]) }}" role="button">@lang('Edit')</a>
  @if($question->is_parent)
    <span class="spacer"></span>
  @else
    <form action="{{ route('topics.questions.destroy', [$topic->id, $question->id]) }}" method="post">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger ml-3" data-confirmation="confirmDeletion">@lang('Delete')</button>
    </form>
  @endif
</div>
