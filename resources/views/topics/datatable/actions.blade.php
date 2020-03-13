<div class="btn-group actions-group float-right" role="group">
  <a class="btn btn-secondary mr-3" href="{{ route('topics.questions.index', $topic->id) }}" role="button">@lang('Questions')</a>
  <a class="btn btn-secondary" href="{{ route('topics.edit', $topic->id) }}" role="button">@lang('Edit')</a>
  <form action="{{ route('topics.destroy', $topic->id) }}" method="post">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger ml-3" data-confirmation="confirmDeletion">@lang('Delete')</button>
  </form>
</div>
