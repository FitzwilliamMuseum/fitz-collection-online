@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('My API Activity') }}</div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Method</th>
                        <th>Path</th>
                        <th>IP</th>
                        <th>Response code</th>
                        <th>Created At</th>
                    </tr>

                    @foreach($activity->items() as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->request_method }}</td>
                            <td>{{ $item->request_full_url }}</td>
                            <td>{{ $item->request_ip }}</td>
                            <td>{{ $item->response_status_code }}</td>
                            <td>{{ $item->created_at }}</td>
                        </tr>
                    @endforeach
                </table>
                <nav aria-label="Page navigation">
                    {{ $activity->appends(request()->except('page'))->links() }}
                </nav>
            </div>
        </div>

        <div class="row justify-content-center text-center">
            <div class="col-md-6">
                <h3 class="text-info">All API activity by method</h3>
                <ul>
                    @foreach($totals as $item)
                        <li>{{ $item->request_method }}: {{ $item->total }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <h3 class="text-info">All API activity by response code</h3>
                <ul>
                    @foreach($codes as $item)
                        <li>{{ $item->response_status_code }}: {{ $item->total }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
