@if(array_key_exists('agents', $data))
    <h3 class="lead collection">
        People, subjects and objects depicted
    </h3>
    <ul class="entities">

        @if(array_key_exists('name', $data))
            @foreach ($data['name'] as $name)
                @if(array_key_exists('reference', $name))
                    <li>
                        <a class="btn btn-sm btn-outline-dark mb-1" href="{{ route('terminology', $name['reference']['admin']['id']) }}">
                            {{ ucfirst($name['reference']['summary_title']) }}
                        </a>
                    </li>
                @else
                    <li>
                        <a class="btn btn-sm btn-outline-dark mb-1" href="#">
                            {{ ucfirst($name['value']) }}
                        </a>
                    </li>
                @endif
            @endforeach
        @endif


        @foreach($data['agents'] as $agent)
            @if(array_key_exists('admin', $agent))
                <li>
                    <a class="btn btn-sm btn-outline-dark mb-1"
                       href="{{ route('agent',$agent['admin']['id'])}}">{{ ucfirst($agent['summary_title'])}}</a>
                </li>
            @else
                <li>
                    {{ ucfirst($agent['summary_title'])}}
                </li>
            @endif
        @endforeach
        @if(array_key_exists('subjects', $data))
            @foreach($data['subjects'] as $subject)
                @if(array_key_exists('admin', $subject))
                    <li>
                        <a class="btn btn-sm btn-outline-dark mb-1"
                           href="{{ route('terminology',$subject['admin']['id'])}}">{{ ucfirst($subject['summary_title'])}}</a>
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
@endif

@if(array_key_exists('project', $data))
    @foreach($data['project'] as $project)
            <h3 class="lead collection">
                Project
            </h3>
            <ul class="entities">
                <li>
                    {{ ucfirst($project['summary_title'])}}
                </li>
            </ul>
    @endforeach
@endif
