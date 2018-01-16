@extends('admin::layouts.master', [
    'title' => 'Forum management'
])

@section('content')
    {{ Breadcrumbs::render('forum::management.index') }}

    @include('forum::management._header')
    @include('forum::management._stats')

    @if(! $selectedCategory)
        <div class="alert alert-danger">Please select manageable category first!</div>
    @else
        @include('forum::management._table')
    @endif
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/forum/admin/js/index.js') }}"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ versionedAsset('assets/forum/admin/css/main.css') }}">
@endsection
