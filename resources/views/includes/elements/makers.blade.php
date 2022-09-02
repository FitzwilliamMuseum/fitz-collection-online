@if(array_key_exists('lifecycle', $data))
    @if(array_key_exists('creation', $data['lifecycle']))
        @if(array_key_exists('maker',$data['lifecycle']['creation'][0]))

            <h3 class="lead collection">
                Maker(s)
            </h3>
            <p>
                @foreach($data['lifecycle']['creation'][0]['maker'] as $maker)
                    @if(array_key_exists('@link', $maker))
                        @if(array_key_exists('role', $maker['@link']))
                            @foreach($maker['@link']['role'] as $role)
                                {{ preg_replace('@\x{FFFD}@u', 'î',(ucfirst($role['value'])))}}:
                            @endforeach
                        @endif
                        @if(array_key_exists('admin', $maker))
                            <a href="{{ route('agent',$maker['admin']['id'])}}">
                                {{ preg_replace('@\x{FFFD}@u', 'î',($maker['summary_title']))}}
                            </a>
                        @else
                            {{ preg_replace('@\x{FFFD}@u', 'î',($maker['summary_title']))}}
                        @endif
                            @if(array_key_exists('@link', $maker))
                                @if(array_key_exists('qualifier',$maker['@link']))
                                    ({{ ucfirst($maker['@link']['qualifier']) }})
                                @endif
                            @endif
                    @endif

                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        @endif
    @endif
@endif
