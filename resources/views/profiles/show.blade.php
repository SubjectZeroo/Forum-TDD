@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ $profileUser->name }}
                            {{-- <small>Since {{ $profileUser->created_at->diffForHumans() }}</small> --}}
                        </h2>
                    </div>
                </div>
                @forelse ($activities as $date => $activity)
                    <div class="card-header">
                        {{ $date }}
                    </div>
                    @foreach ($activity as $record)
                        @if (view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}", [
                                'activity' => $record,
                            ])
                        @endif
                    @endforeach

                @empty
                    <p>There is no activity for this user</p>
                @endforelse
                {{-- {{ $threads->links() }} --}}
            </div>
        </div>

    </div>
@endsection
