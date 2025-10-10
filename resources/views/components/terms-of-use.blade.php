<h3>Terms of use</h3>

<p class="text-dark">Copyright © The Fitzwilliam Museum</p>

<p class="text-dark">For copyright information contact
    <a href="{{ env('MAIN_URL') }}/commercial-services/image-library">Fitzwilliam Museum Image Library</a>
</p>

<p class="text-dark">
    The low-resolution images published on this Website are made available under a <a href="https://creativecommons.org/">Creative Commons Attribution</a> licence (CC BY-NC-ND).
    For more details: <a href="{{ env('MAIN_URL') }}/about-us/terms-of-use-of-our-website">Fitzwilliam Terms of Use</a>
</p>

<p class="text-dark">This licence does not include any images of works that are still in copyright. Artistic copyright extends from the life of the artist to 70 years from the end of the calendar year in which the artist died.</p>

<a class="btn btn-sm btn-sm btn-dark m-1 d-block"
   href="{{ env('APP_URL') }}/imagestore/{{ $path }}"
   target="_blank"
   download="{{ basename($path) }}">@svg('fas-download',['width'=>'15','class' => 'mr-2'])
    Download this image</a>

<p class="text-dark">
    For further information on use of images or to license a high resolution version, <a href="{{ env('MAIN_URL')}}/commercial-services/image-library">please contact our image library</a> who can discuss terms and fees.
</p>
