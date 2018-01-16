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
                <select name="category_id" class="form-control select2-categories">
                    <option value="" disabled {{ $selectedCategory ?? 'selected' }}>-----</option>
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