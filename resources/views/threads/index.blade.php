@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @forelse ($threads as $thread)
                    <div class="card mt-2">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4>
                                    <a href="{{ $thread->path() }}"> {{ $thread->title }}</a>
                                </h4>
                                <a href="{{ $thread->path() }}">
                                    {{ $thread->replies_count }}
                                    {{ Str::plural('reply', $thread->replies_count) }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="body">{{ $thread->body }}</div>
                        </div>
                    </div>
                @empty
                    <p>There are no relevant results at this time.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
