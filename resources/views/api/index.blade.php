@extends('layouts.layout')
@section('title','The Fitzwilliam Museum Collections API')
@section('content')
@markdown

The Fitzwilliam Museum Applications Programming Interface (API) API provides JSON-API data as a REST-style service
that should enable you to reuse, remix, analyse or incorporate our data into your work or projects.

### What does the API cover?

Our API gives you access to the following data through our current version (V1):

* Objects (e.g. paintings, sculptures, etc.)
* Images (static and deep zooming - IIIF)
* Terminologies - (e.g. subjects, keywords, etc.)
* Actors and Agents (eg. makers, contributors, etc. - not many of these have biographies)
* Places (eg. museums, institutions, towns etc. - some of these have coordinates, geocoded via Nominatim)

### What is this API for?

Serendipity, creativity, and the ability to reuse our data in your work or projects. Maybe you are a museum professional,
or a student or a teacher, or a researcher. You can access our data through the API, and use it to create your own projects.

### How are these data licensed?

Our structured metadata have been licensed openly under a Creative Commons Zero license for over 10 years, but our images
are made available under a more restrictive license - Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International license.
This may change in the future. So to reiterate:]

* CCO - Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International license **FOR DATA**
* CC-BY-NC-SA - Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International license **FOR IMAGES**

20th century and in copyright works generally are not included in this system.

### What is this API built on?

This API is a wrapper around the Knowledge Integration CIIM software output derived from our Axiell Collections database and
Portfolio DAMS, with formatting and enrichment applied. It is written in PHP 8.1 and uses the Laravel framework 9. Code can be found
on our Github repository linked in the footer of this page.

### Do we want to know what you're doing?

Yes please, as Merete Sanderhoff says, [Sharing is Caring](https://www.youtube.com/watch?v=whkUK3xaoPQ). We want to know what
you're doing with our data, show us, tell us, inform us, show others etc. Our system stands on the shoulder of giants, maybe
your work will too.

### Can I just download all your data?

Yes! You can get all our structured data in one archive. Our data is available on [Github](https://github.com/fitzwilliammuseum/fitz-collections-dump)
as version controlled json files.

### Can I download all your images?

Well you could try! It's several of Terabytes!

### Is your API protected?

Authenticated: Our API is protected via authentication (via session cookie/bearer token), so you need to login to access it. You can do this by creating an account via a web interface or
via programmatic means. This means we can monitor use and track problems (hopefully you won't find any!)
So after creating an [account](/api/login), you can use the API to access your data. If you are going to use the system frequently,
your IP address can be added to our list of allowed IP addresses and you won't need to authenticate again.

Rate limited: Unless your address is whitelisted, you can only access the API 60 times per minute. If you are whitelisted, your
API access is 300 requests per minute. The rate limit is applied to all API calls except the welcome route, and the rate limit available
to use is returned in the header response as below:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

### What data do you collect? What do you use it for?

We collect data about you (email address, your name and IP address), your use of our API, and your use of our images.
We use these data to improve our service and to make it more useful for you. These data are stored in a database on AWS
in the EU-West region. If you want to have all your data removed, we will do our best to identify your requests and user
account and remove it. At the moment we're likely to only use these data to report how many calls have been made to our API.

Data we collect includes:

* Your IP address
* Your user id for each call
* Data of each request made (method and parameters)

### Have you documented your API?

Yes! We have in different ways. This will give you an idea of how to use it. We assume you have basic knowledge of
programming or data science.

* [Swagger UI](https://data.fitzmuseum.cam.ac.uk/api/v1/docs).
* Downloadable Postman collection
* R notebooks with examples
* Python notebooks with examples
@endmarkdown

@endsection
