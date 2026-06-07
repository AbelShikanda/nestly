@props(['gallery', 'slideId'])

<div class="thumbnail-strip" id="thumbstrip-{{ $slideId }}">
    <div class="thumb-label">📸</div>
    @foreach($gallery as $index => $item)
        <div class="thumbnail {{ $index === 0 ? 'active-thumb' : '' }}" data-type="{{ $item['type'] }}" data-url="{{ $item['url'] }}">
            @if($item['type'] === 'image')
                <img src="{{ $item['url'] }}" alt="thumbnail">
            @else
                <video src="{{ $item['url'] }}" muted loop playsinline></video>
            @endif
        </div>
    @endforeach
</div>