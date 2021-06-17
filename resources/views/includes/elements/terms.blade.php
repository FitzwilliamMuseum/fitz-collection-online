<h3 class="lead collection">Terms of use</h3>
<p class="text-info">
  These images are provided for non-commercial use under a Creative Commons
  License (BY-NC-ND). To license a high resolution version, please contact
  our <a href="{{ env('MAIN_URL')}}/commercial-services/image-library">image library</a>
   who will discuss fees, terms and fee waivers.
</p>
<a class="btn btn-sm btn-sm btn-dark m-1 d-bloc"
href="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['large']['location'] }}"
target="_blank"
download="{{ basename($record['_source']['multimedia'][0]['processed']['large']['location'] ) }}"><i class="fas fa-download mr-2"></i>
Download this image</a>
