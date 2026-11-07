@if(($pages ?? 0) > 0)
<div class="pager">
 @if($page > 1)
  <a class="btn" href="{{ request()->fullUrlWithQuery([$param => $page - 1]) }}">Previous</a>
 @else
  <span class="btn off" aria-disabled="true">Previous</span>
 @endif
 <span class="muted">Page {{ $page }} of {{ $pages }}</span>
 @if($page < $pages)
  <a class="btn" href="{{ request()->fullUrlWithQuery([$param => $page + 1]) }}">Next</a>
 @else
  <span class="btn off" aria-disabled="true">Next</span>
 @endif
</div>
@endif
