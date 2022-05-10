@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('threads._list')
                <div class="mt-3">
                    {{ $threads->render() }}
                </div>

            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Search
                    </div>
                    <div class="card-body">
                        <form method="GET" action="/threads/search">
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Search form something.." name="q">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
