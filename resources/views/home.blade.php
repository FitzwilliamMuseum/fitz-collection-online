@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('My API Activity') }}</div>
                    </div>
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Method</th>
                        <th>Path</th>
                        <th>IP</th>
                        <th>Created At</th>
                    </tr>

                @foreach($activity->items() as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->request_method }}</td>
                        <td>{{ $item->request_full_url }}</td>
                        <td>{{ $item->request_ip }}</td>
                        <td>{{ $item->created_at }}</td>
                    </tr>
                @endforeach
                </table>
                <nav aria-label="Page navigation">
                    {{ $activity->appends(request()->except('page'))->links() }}
                </nav>
                </div>
            </div>
        </div>
@endsection
