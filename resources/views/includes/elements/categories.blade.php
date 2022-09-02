@if(array_key_exists('categories', $data))
    <h3 class="lead collection">
        Categories
    </h3>
    <ul>
        @foreach($data['categories'] as $category)
            <li><a href="{{route('terminology',[$category['admin']['id']])}}">{{ ucfirst($category['summary_title']) }}</a></li>
        @endforeach
    </ul>
@endif
