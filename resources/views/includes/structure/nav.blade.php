<!-- Nav bars -->
<nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText"
  aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarText">
  <ul class="navbar-nav mr-auto">

    <li class="nav-item active">
      <a class="nav-link" href="{{ URL::to('https://beta.fitz.ms/') }}">Home <span class="sr-only">(current)</span></a>
    </li>

    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAbout" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        About</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownAbout">
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/about-us') }}">About the Museum</a>
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/about-us/collections') }}">Collection areas</a>
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/about-us/departments') }}">Departments</a>
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/news') }}">News</a>
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/about-us/press-room') }}">Press room</a>
          <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/objects-and-artworks/image-library/') }}">Image library</a>
        </div>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="https://tickets.museums.cam.ac.uk">Events and tickets</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownVisit" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Visit</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownVisit">
          <a class="dropdown-item" href="https://beta.fitz.ms/visit-us">Your visit</a>
          <a class="dropdown-item" href="https://beta.fitz.ms/visit-us/exhibitions">Exhibitions</a>
          <a class="dropdown-item" href="https://beta.fitz.ms/visit-us/galleries">Galleries</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCollections" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Collections</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownCollections">
            <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/objects-and-artworks/') }}">An introduction</a>
            <a class="dropdown-item" href="{{ URL('https://collection.beta.fitz.ms') }}">Search our collection</a>
            <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/objects-and-artworks/highlights/') }}">Collection highlights</a>
            <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/objects-and-artworks/audio-guide/') }}">Audio guide</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLearning" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Learning</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownLearning">
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning') }}">Learning</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/families') }}">Families</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/young-people') }}">Young people</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/school-sessions') }}">Schools</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/adult-programming') }}">Adults</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/community-programming') }}">Communities</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/group-activities') }}">Groups</a>
              <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/learning/resources') }}">Resources</a>
            </div>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="{{ URL::to('https://beta.fitz.ms/support-us') }}">Support</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownResearch" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Our research</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownResearch">
                <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/research') }}">Research at the museum</a>
                <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/research/projects') }}">Research projects</a>
                <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/research/staff-profiles') }}">Researcher profiles</a>
                <a class="dropdown-item" href="{{ URL::to('https://beta.fitz.ms/research/online-resources/') }}">Online resources</a>
              </div>
            </li>
          </ul>
          {{ \Form::open(['url' => url('search/results'),'method' => 'GET', 'class' => 'form-inline ml-auto']) }}
          <label for="search" class="sr-only">Search: </label>
          <input id="query" name="query" type="text" class="form-control mr-sm-2"
          placeholder="Search our site" required value="{{ old('query') }}" aria-label="Your query">
          <button type="submit" class="btn btn-outline-light" id="searchButton" aria-label="Submit your search">Search</button>
          {!! Form::close() !!}
        </div>
      </nav>
