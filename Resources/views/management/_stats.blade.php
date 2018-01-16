<div class="row">
    <div class="col-md-4">
        <div class="box bg-info">
            <div class="box-cell p-a-3 valign-middle">
                <i class="box-bg-icon middle right fa fa-list-alt"></i>
                <span class="font-size-24"><strong>{{ $stats->get('threadsCount') }}</strong></span><br>
                <span class="font-size-15">Total threads</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box bg-danger">
            <div class="box-cell p-a-3 valign-middle">
                <i class="box-bg-icon middle right fa fa-comments-o"></i>
                <span class="font-size-24"><strong>{{ $stats->get('postsCount') }}</strong></span><br>
                <span class="font-size-15">Total posts</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @php
            $latestThread = $stats->get('latestThread');
        @endphp

        <a href="{{ $latestThread ? route('forum::admin.management.threads.edit', $latestThread) : 'javascript:;' }}" class="box bg-success">
            <div class="box-cell p-a-3 valign-middle">
                <i class="box-bg-icon middle right fa fa-bullhorn"></i>
                <span class="font-size-24"><strong>{{ str_limit($latestThread->title ?? '-', 30) }}</strong></span><br>
                <span class="font-size-15">Latest thread</span>
            </div>
        </a>
    </div>
</div>