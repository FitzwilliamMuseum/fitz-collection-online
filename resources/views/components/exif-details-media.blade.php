@if(!empty($exif))
    @if(!empty($exif->getCopyright()) )
        <p class="text-info">{{ str_replace('Â','',utf8_encode($exif->getCopyright())) }}</p>
    @endif
@endif
