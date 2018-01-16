<div class="btn-group">
    <button type="button" class="btn btn-xs btn-info btn-outline btn-outline-colorless dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        Options
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="javascript:;" class="toggle-thread" data-attribute="is_locked" data-thread-id="{{ $thread->id }}">
                <i class="fa fa-{{ $thread->is_locked ? 'unlock' : 'lock' }}"></i>
                {{ $thread->is_locked ? 'Unlock thread' : 'Lock thread' }}
            </a>
        </li>

        <li>
            <a href="javascript:;" class="toggle-thread" data-attribute="is_pinned" data-thread-id="{{ $thread->id }}">
                <i class="fa fa-{{ $thread->is_pinned ? 'times' : 'check' }}"></i>
                {{ $thread->is_pinned ? 'Unpin thread' : 'Pin thread' }}
            </a>
        </li>
    </ul>
</div>

<a href="/" class="btn btn-xs btn-warning">
    <i class="fa fa-edit"></i> Edit
</a>

<a href="javascript:;"
   class="btn btn-xs btn-{{ $thread->trashed() ? 'success' : 'danger' }} toggle-thread"
   data-attribute="deleted_at"
   data-thread-id="{{ $thread->id }}"
>
    <i class="fa fa-{{ $thread->trashed() ? 'refresh' : 'trash' }}"></i>
    {{ $thread->trashed() ? 'Recover' : 'Delete' }}
</a>

