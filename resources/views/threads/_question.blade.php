<div class="card" v-if="editing">
    <div class="card-header">
        <div class="d-flex">

            {{-- <img src="{{ asset($thread->creator->avatar_path) }}" alt="{{ $thread->creator->name }}" width="25"
                height="25" class="mr-1"> --}}

            {{-- <a href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a>
            posted:
            {{ $thread->title }} --}}

            <input v-model="form.title" type="text" value="{{ $thread->title }}" class="form-control">

        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            {{-- <textarea class="form-control" rows="10" v-model="form.body">{{ $thread->body }}</textarea> --}}
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-xs" @click="editing = true" v-show="! editing">Edit</button>
        <button class="btn btn-xs btn-primiry" @click="update">Update</button>
        <button class="btn btn-xs" @click="resetForm">Cancel</button>
        @can('update', $thread)
            <form method="POST" action="{{ $thread->path() }}" class="ml-a">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">Delete Thread</button>
            </form>
        @endcan
    </div>
</div>


<div class="card" v-else>
    <div class="card-header">
        <div class="d-flex flex-column">
            <div>
                <template v-if="editing"></template>
                <span v-text="title"></span>
            </div>
            <div> posted by:
                <img src="{{ asset($thread->creator->avatar_path) }}" alt="{{ $thread->creator->name }}" width="25"
                    height="25">
                <a href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a>

            </div>



            {{-- {{ $thread->title }} --}}
            {{-- @can('update', $thread)
                <form method="POST" action="{{ $thread->path() }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">Delete Thread</button>
                </form>
            @endcan --}}
        </div>
    </div>
    <div class="card-body" v-html="body">
        {{-- {{ $thread->body }} --}}
    </div>
    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-xs" @click="editing = true">Edit</button>
    </div>
</div>
