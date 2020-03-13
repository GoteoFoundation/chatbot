<div class="btn-group actions-group float-right" role="group">
  @unless($language->is_parent)
    <form action="{{ route('languages.first', $language->id) }}" method="post">
      @csrf
      @method('PATCH')
      <button type="submit" class="btn btn-outline-secondary mr-3" data-href="/delete.php?id=23" data-confirmation="confirmMain">@lang('Set As Main')</button>
    </form>
  @endunless
  <a class="btn btn-secondary" href="{{ route('languages.edit', $language->id) }}" role="button">@lang('Edit')</a>
  @if($language->is_parent)
    <span class="spacer"></span>
  @else
    <form action="{{ route('languages.destroy', $language->id) }}" method="post">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger ml-3" data-confirmation="confirmDeletion">@lang('Delete')</button>
    </form>
  @endif
</div>
