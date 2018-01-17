@extends('admin::layouts.master')

@section('content')
    {{ Breadcrumbs::render('forum::threads.edit', $thread) }}

    {{-- @TODO --}}

    <div class="panel panel-default" id="thread-app">
        <div class="panel-heading">
            <span class="panel-title">Thread #{{ $thread->id }}</span>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label class="control-label">Thread title:</label>
                <a href="javascript:" class="editable">{{ $thread->title }}</a>
            </div>

            <div class="form-group">
                <label class="control-label">Thread content:</label>
                <thread-post :post="{{ $thread->firstPost }}"
                             update="{{ route('forum::admin.management.posts.update', [$thread, $thread->firstPost]) }}">
                </thread-post>
            </div>

        </div>
    </div>

@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/forum/sceditor/minified/themes/default.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/forum/sceditor/minified/jquery.sceditor.min.js') }}"></script>
    <script src="{{ asset('assets/forum/sceditor/minified/jquery.sceditor.bbcode.min.js') }}"></script>
    <script src="{{ versionedAsset('assets/forum/admin/js/thread-edit.js') }}"></script>
@endsection