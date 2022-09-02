<a href="{{ env('APP_URL')}}/imagestore/{{ $path }}" download="{{ basename($path ) }}" class="btn btn-dark m-1 mt-3 mb-3 p-2">
    @svg('fas-download', ['width' => 15]) Download this image
</a>
