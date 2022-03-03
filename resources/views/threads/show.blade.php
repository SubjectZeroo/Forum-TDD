@extends('layouts.app')

@section('content')
    <thread-view initial-replies-count="{{ $thread->replies_count }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <a href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a>
                                posted:
                                {{ $thread->title }}
                                @can('update', $thread)
                                    <form method="POST" action="{{ $thread->path() }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger">Delete Thread</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            {{ $thread->body }}
                        </div>
                    </div>

                    <replies :data="{{ $thread->replies }}" @removed="repliesCount--"></replies>

                    @if (auth()->check())
                        <div class="card mt-2">
                            <div class="card-body">
                                <form method="POST" action="{{ $thread->path() . '/replies' }}">
                                    @csrf
                                    <div class="form-group">
                                        <textarea name="body" id="body" class="form-control"
                                            placeholder="Have something to say?" rows="5"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Post</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p> Please <a href="/login">sign in</a> to participate in this discussion. </p>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            This thread was published {{ $thread->created_at->diffForHumans() }} by
                            <a href="#">{{ $thread->creator->name }}</a>, and currently has
                            <span v-text="repliesCount"></span>
                            {{ Str::plural('comment', $thread->replies_count) }}.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
