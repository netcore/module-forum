@extends('admin::layouts.master')

@section('content')
    {{ Breadcrumbs::render('forum::threads.edit', $thread) }}

    {{-- @TODO --}}

@endsection