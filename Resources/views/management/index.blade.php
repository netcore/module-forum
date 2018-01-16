@extends('admin::layouts.master', [
    'title' => 'Forum management'
])

@section('content')
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="index.html">Home</a></li>
        <li class="active">Dashboard</li>
    </ol>

    <div class="page-header">
        <div class="row">
            <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                <h1><i class="page-header-icon fa fa-bullhorn"></i> Forum management</h1>
            </div>

            <hr class="page-wide-block visible-xs visible-sm">

            <form action="{{ route('forum::admin.management.index') }}" class="page-header-form col-xs-12 col-md-8 pull-md-right" id="category-select">
                <div class="input-group category-select">
                    <span class="input-group-addon b-a-1 b-r-0">
                        Select category
                    </span>
                    <select name="category_id" class="form-control" onchange="document.getElementById('category-select').submit()">
                        <option value="" disabled {!! !$selectedCategory ? 'selected' : '' !!}>-- None --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {!! $selectedCategory && $category->id == $selectedCategory->id ? 'selected' : '' !!}>
                                {{ $category->chainedName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @include('forum::management._stats')

    @if(! $selectedCategory)
        <div class="alert alert-danger">Please select manageable category first!</div>
    @else
        <table class="table table-bordered" id="threads-datatable" data-route="{{ route('forum::admin.management.paginate', $selectedCategory) }}">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Replies</th>
                    <th>Views</th>
                    <th>Is locked</th>
                    <th>Is pinned</th>
                    <th>Created at</th>
                    <th>Deleted at</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    @endif

@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/forum/admin/js/index.js') }}"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ versionedAsset('assets/forum/admin/css/main.css') }}">
@endsection
