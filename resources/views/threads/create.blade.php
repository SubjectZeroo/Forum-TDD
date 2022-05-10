@extends('layouts.app')

@section('head')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create a New Thread</div>

                    <div class="card-body">
                        <form method="POST" action="/threads">
                            @csrf
                            <div class="form-group">
                                <label for="channel_id">Channel</label>
                                <select name="channel_id" id="channel_id" class="form-select"
                                    aria-label="Default select example">
                                    <option selected>Select a channel</option>
                                    @foreach ($channels as $channel)
                                        <option value="{{ $channel->id }}"
                                            {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                            {{ $channel->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input name="title" type="text" class="form-control" id="title" placeholder="Title"
                                    value="{{ old('title') }}">
                            </div>
                            <div class="form-group">
                                <label for="body">Body</label>
                                {{-- <textarea name="body" id="body" class="form-control" rows="8">{{ old('body') }}</textarea> --}}
                                <wysiwyg name="body"></wysiwyg>
                            </div>
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6Lc-nK0fAAAAABHKm27afQaEeqZC8xU4Ohlqgary
                                                    "></div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Publish</button>
                            </div>

                            @if (count($errors))
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
