<h3 class="lead collection">About this image</h3>
<div class="col-12 p-3 mx-auto mb-3 rounded">
    @if(!empty($exif->getHeadline()))
        @section('title', ucfirst($exif->getHeadline()))
    @elseif(array_key_exists('title', $object))
        @section('title', ucfirst($object['title'][0]['value']))
    @elseif(array_key_exists('summary_title', $object))
        @section('title', ucfirst($object['summary_title']))
    @else
        @section('title', 'No title available')
    @endif

    @if(!empty($exif->getCaption()))
        @section('description', $exif->getCaption())
    <p>
        {{ $exif->getCaption() }}
    </p>
    @endif


    <h3 class="lead collection">Image data</h3>
    <ul>
        @if(!empty($exif->getTitle()))
            <li>Accession Number: {{ $exif->getTitle() }}</li>
        @endif
        @php
            $raw = $exif->getRawData()
        @endphp
        @if(array_key_exists('COMMENT', $raw) && is_array($raw['COMMENT']))
            @foreach($raw['COMMENT'] as $com)
                <li>{{ utf8_encode($com) }}</li>
            @endforeach
        @endif
        @if(!empty($exif->getCopyright()) )
            <li>{{ str_replace('Â','',utf8_encode($exif->getCopyright())) }}</li>
        @else
            <li>Copyright © The Fitzwilliam Museum</li>
        @endif

        @if(!empty($exif->getAperture()))
            <li>Aperture: {{ $exif->getAperture() }}</li>
        @endif
        @if(!empty($exif->getFocalLength()))
            <li>Focal length: {{  $exif->getFocalLength() }}
        @endif
        @if(!empty($exif->getCamera()))
            <li>Camera: {{ $exif->getCamera() }}</li>
        @endif
        @if(!empty($exif->getAuthor()))
            <li>Photographer name: {{ $exif->getAuthor() }}</li>
        @endif
        @if(!empty($exif->getHeight()))
            <li>Image height: {{ $exif->getHeight() }} pixels</li>
        @endif
        @if(!empty($exif->getWidth()))
            <li>Image width: {{ $exif->getWidth() }} pixels</li>
        @endif
        @if(!empty($exif->getSoftware()))
            <li>Processed with: {{  $exif->getSoftware() }}</li>
        @endif
        @if(!empty($exif->getFileSize()))
            <li>Filesize: @humansize($exif->getFileSize())</li>
        @endif

        @php
            $raw = $exif->getRawData()
        @endphp
        @if(!empty($raw))
            @if(array_key_exists('ExposureTime', $raw))
                <li>Exposure time: {{ $raw['ExposureTime'] }}</li>
            @endif
            @if(array_key_exists('ISOSpeedRatings', $raw))
                <li>ISO Speed: {{ $raw['ISOSpeedRatings'] }}</li>
            @endif
            @if(array_key_exists('FNumber', $raw))
                <li>Fnumber: {{ $raw['FNumber'] }}</li>
            @endif
            @if(array_key_exists('DateTimeOriginal', $raw))
                <li>Captured: {{ $raw['DateTimeOriginal'] }}</li>
            @endif
        @endif
    </ul>

    @if(!empty($exif->getKeywords()) && is_array($exif->getKeywords()))
        <h3 class="collection lead">Key words</h3>
        <div id="keywords">
            @foreach ($exif->getKeywords() as $key)
                <span class="badge badge-dark">{{ $key }}</span>
            @endforeach
        </div>
    @endif
</div>
