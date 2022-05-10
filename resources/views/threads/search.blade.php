@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <ais-index app-id="{{ config('scout.algolia.id') }}" api-key="{{ config('scout.algolia.key') }}"
                index-name="threads" query="{{ request('q') }}">
                <div class="cold-md-4">
                    <ais-search-box placeholder="Find Threads.." :autofocus="true" class="form-control">
                    </ais-search-box>
                </div>
                <div class="col-md-8">

                    <ais-results>
                        <template scope="{ result }">
                            <li>
                                <a href="result.path">
                                    <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                                </a>
                            </li>
                        </template>
                    </ais-results>

                </div>


                {{-- <div class="col-md-4">
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

            </div> --}}
            </ais-index>
        </div>
    </div>
@endsection
