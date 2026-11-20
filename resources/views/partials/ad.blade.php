@if($ads->isNotEmpty())
<div class="ad-zone {{ $format ?? 'banner' }} reveal" data-testid="{{ $tid ?? 'ad-zone' }}">
  @foreach($ads as $ad)
    <a href="{{ $ad->link_url ? route('ad.click', $ad) : '#' }}" @if($ad->link_url) target="_blank" rel="sponsored noopener" @endif data-testid="ad-{{ $ad->id }}">
      <img src="{{ $ad->image_url }}" alt="{{ $ad->name }}" loading="lazy">
      <span class="ad-label">Iklan</span>
    </a>
  @endforeach
</div>
@endif
