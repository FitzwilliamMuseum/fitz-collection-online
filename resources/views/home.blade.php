@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-12">
                <h3 class="text-info">My API Activity</h3>
                @if($activity->items())
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                            <tr>
                                <th>Method</th>
                                <th>Path</th>
                                <th>IP</th>
                                <th>Response code</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            @foreach($activity->items() as $item)
                                <tr>
                                    <td>{{ $item->request_method }}</td>
                                    <td><a href="{{ $item->request_full_url }}">{{ $item->request_full_url }}</a></td>
                                    <td>{{ $item->request_ip }}</td>
                                    <td>{{ $item->response_status_code }}</td>
                                    <td>{{ $item->created_at }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <nav aria-label="Page navigation">
                        {{ $activity->appends(request()->except('page'))->links() }}
                    </nav>
                @else
                    <p>You haven't used the API yet</p>
                @endif
            </div>
        </div>

        <div class="row justify-content-center text-center">
            <div class="col-md-6">
                <h3 class="text-info">All API activity by method</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Method</th>
                        <th>Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($totals as $item)
                        <tr>
                            <td>{{ $item->request_method }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h3 class="text-info">All API activity by response code</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Response code</th>
                        <th>Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($codes as $item)
                        <tr>
                            <td>{{ $item->response_status_code }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
