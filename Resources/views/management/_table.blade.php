<table class="table table-bordered" id="threads-datatable"
       data-route="{{ route('forum::admin.management.paginate', $selectedCategory) }}">
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