<!-- Breadcrumb done by @sina-rzp -->
<div class="container">
<nav aria-label="breadcrumb bg-white">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ URL::to('https://beta.fitz.ms') }}">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ URL::to('https://collection.beta.fitz.ms') }}">Objects and Artworks</a></li>
    @php
    $bread = URL::to(env('MAIN_URL'));
    $link = Request::path();
    $subs = explode("/", $link);
    @endphp

    @if (Request::path() != '/')
      @for($i = 0; $i < count($subs); $i++)
        @php
        $bread = $bread."/".$subs[$i];
        $title = urldecode($subs[$i]);
        $title = ucwords($title);
        if(is_numeric($title)){
          if(array_key_exists(0,$data)){
            if(array_key_exists('accession_number', $data[0]['_source']['identifier'][0])){
              $title = $data[0]['_source']['identifier'][0]['accession_number'];
            }
          }
        }
        @endphp
        @if($title != 'Id')
        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        @endif
      @endfor
   @endif
  </ol>
</nav>
</div>
