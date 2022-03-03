@component('profiles.activities.actvity')
    @slot('heading')
        <a href="{{ $activity->subject->favortited->path() }}">{{ $profileUser->name }} favorited a reply</a>
        {{-- <a
            href="{{ $activity->subject->thread->path }}">"{{ $activity->subject->thread->title }}"</a> --}}
    @endslot
    @slot('body')
        {{ $activity->subject->favorited->body }}
    @endslot
@endcomponent
