@extends('admin::layouts.master')

@section('content')
    {{ Breadcrumbs::render('forum::threads.edit', $thread) }}

    <div class="panel panel-default" id="thread-app"
         data-route="{{ route('forum::admin.management.threads.paginate', $thread) }}">
        <div class="panel-heading">
            <span class="panel-title">
                <a href="javascript:" class="editable" data-pk="{{ $thread->id }}" data-name="title"
                   data-url="{{ route('forum::admin.management.threads.x-editable', $thread) }}">
                    {{ $thread->title }}
                </a>
            </span>
        </div>

        <div class="panel-body">
            <thread-post v-for="post in posts.data" :key="post.id" :post="post"></thread-post>
        </div>

        <div class="panel-footer text-right">
            <pagination :pagination="posts" @paginate="loadPosts()" :offset="10"></pagination>
        </div>

        @include('forum::threads.modals.blacklist')
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/forum/sceditor/minified/themes/default.min.css') }}">
    <style type="text/css">
        .select2-container {
            z-index: 100000;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/forum/sceditor/minified/jquery.sceditor.min.js') }}"></script>
    <script src="{{ asset('assets/forum/sceditor/minified/jquery.sceditor.bbcode.min.js') }}"></script>
    <script src="{{ versionedAsset('assets/forum/admin/js/thread-edit.js') }}"></script>
@endsection