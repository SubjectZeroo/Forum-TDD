@forelse ($threads as $thread)
    <div class="card mt-2">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4>
                        <a href="{{ $thread->path() }}">
                            @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                <strong>{{ $thread->title }}</strong>
                            @else
                                {{ $thread->title }}
                            @endif
                        </a>
                    </h4>
                    <h5>Posted By <a
                            href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a>
                    </h5>
                </div>


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
