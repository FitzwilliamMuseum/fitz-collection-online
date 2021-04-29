@php
$pris = Arr::pluck($data['identifier'],'priref');
$pris = array_filter($pris);
$pris= Arr::flatten($pris);
$access = Arr::pluck($data['identifier'],'accession_number');
$access = array_filter($access);
$access= Arr::flatten($access);
$uri = Arr::pluck($data['identifier'],'uri');
$uri = array_filter($uri);
$uri= Arr::flatten($uri);
@endphp
IDENTIFIERS
-----------
id:	{{ $pris[0] }}
accession number:	{!! $access[0] !!}

DATE AUDIT
----------
created:	{{ \Carbon\Carbon::createFromTimestamp($data['admin']['created']/ 1000)->format('l j F Y') }}
updated:	{{ \Carbon\Carbon::createFromTimestamp($data['admin']['modified']/ 1000)->format('l j F Y') }}

DESCRIPTIVE DATA
----------------
@isset($data['description'])
@php
$desc = array_reverse($data['description']);
@endphp
@foreach ($desc as $key)
object type: {{ $key['value'] }}
@endforeach
@endisset
@isset($data['summary_title'])
title:	{{ $data['summary_title']}}
@endisset
@isset($data['note'])

NOTES
-----
@foreach ($data['note'] as $note)
@foreach($note as $key => $value)
{{ $key  }}: {!! $value!!}
@endforeach
@endforeach

@endisset

LICENSING
---------
text license status:	CC0
image license status:	CC-BY-NC-SA

@isset($data['institutions'])
OWNERSHIP
---------
@foreach($data['institutions'] as $institution)
instutition: {{ $institution['summary_title'] }}
@endforeach
@endisset
@isset($data['department']['value'])
department: {{ $data['department']['value'] }}
@endisset
@isset($data['collection'])
collection:	{{ $data['collection'][0]['summary_title']}}
@endisset
@isset($data['legal'])
creditline: {{ $data['legal']['credit_line'] }}
@endisset

STABLE URL
----------
url:	{{ $uri[0]}}

@isset($data['agents'])
PEOPLE
-------------------
@foreach ($data['agents'] as $agent)
{{ $agent['summary_title'] }}
@endforeach
@endisset

@isset($data['subjects'])
SUBJECTS
-------------------
@foreach ($data['subjects'] as $subject)
{{ $subject['summary_title'] }}
@endforeach
@endisset

@isset($data['components'])
COMPONENTS
----------
@foreach ($data['components'] as $component)
{{ $component['summary_title'] }}
@endforeach
@endisset

@isset($data['techniques'])

@foreach ($data['techniques'] as $technique)
TECHNIQUES
----------
@isset($technique['description'])
@foreach($technique['description'] as $k => $v)
{{$v['value']}}
@endforeach
@endisset
{{$technique['reference']['summary_title']}}
@endforeach
@endisset

@isset($data['categories'])
CATEGORIES
------
@foreach ($data['categories'] as $category)
category: {{ $category['summary_title'] }}
@endforeach
@endisset

@isset($data['school_style'])
SCHOOL OR STYLE
---------------
@foreach ($data['school_style'] as $school)
{{ $school['summary_title'] }}
@endforeach
@endisset
@isset($data['lifecycle']['creation'])
DATING
------
@isset($data['lifecycle']['creation'][0]['date'])
creation date:	{{ $data['lifecycle']['creation'][0]['date'][0]['earliest'] }} - {{ $data['lifecycle']['creation'][0]['date'][0]['latest'] }}
creation date earliest:	{{ $data['lifecycle']['creation'][0]['date'][0]['earliest'] }}
creation date latest:	{{ $data['lifecycle']['creation'][0]['date'][0]['latest'] }}
@endisset
@endisset
@isset($data['lifecycle']['creation'][0]['periods'])
@foreach ($data['lifecycle']['creation'][0]['periods'] as $period)
culture:	{{ $period['summary_title'] }}
@endforeach
@endisset

@isset($data['lifecycle']['creation'][0]['maker'])
CREATORS
--------
@foreach ($data['lifecycle']['creation'][0]['maker'] as $maker)
maker: {{ $maker['summary_title'] }}
@endforeach
@endisset

@isset($data['measurements'])
DIMENSIONS
----------
@foreach ($data['measurements']['dimensions'] as $dims)
@foreach ($dims as $key => $value)
{{ $key }}: {{ $value }}
@endforeach

@endforeach

@endisset
@isset($data['inscriptions'])
INSCRIPTIONS
------------
@foreach ($data['inscriptions'] as $inscription)
inscription:
@endforeach
@endisset

@isset($data['exhibitions'])
EXHIBITIONS HISTORY
-------------------
@foreach ($data['exhibitions'] as $exhibition)
title:	{{ $exhibition['summary_title'] }}
@endforeach
@endisset
@isset($data['publications'])

CITATIONS
@foreach ($data['publications'] as $publication)
{!! $publication['summary_title'] !!}
@endforeach
---
@endisset
@isset($data['multimedia'])
IMAGES
@foreach ($data['multimedia'] as $image)
{{-- @dd($image['processed']) --}}
@foreach($image['processed'] as $key => $value)
surrogate: {{ $key }}
@isset($value['format'])
format: {{ $value['format'] }}
@endisset
@isset($value['location'])
location: {{ env('APP_URL')}}/imagestore/{{ $value['location'] }}
@endisset
@isset($value['measurements']['dimensions'])
@foreach($value['measurements']['dimensions'] as $measurement)
{{ $measurement['dimension'] }}: {{ $measurement['value'] }} {{ $measurement['units'] }}
@endforeach
@endisset

@endforeach
@endforeach
@endisset
