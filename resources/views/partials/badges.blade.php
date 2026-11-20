@if($p)
<div class="badges">
  @if($p->category)<span class="badge {{ ($red ?? false) ? 'red' : '' }}">{{ $p->category->name }}</span>@endif
  @if($p->region)<span class="badge soft">{{ $p->region->name }}</span>@endif
</div>
@endif
