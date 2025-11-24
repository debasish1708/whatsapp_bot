<div class="d-flex align-items-center gap-3">
    <a href="{{route('accounts.delete-request.show', $user->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>

    <button 
        type="button" 
        class="btn btn-sm btn-icon" 
        title="Approve" 
        onclick="handleStatusButtons('{{route('accounts.delete-request.accept', $user->id)}}', 'approve')"
    >
        <span class="ti-sm ti ti-check text-success"></span>
    </button>
    <button 
        type="button" 
        class="btn btn-sm btn-icon text-warning" 
        title="Reject"
        onclick="handleStatusButtons('{{route('accounts.delete-request.reject', $user->id)}}', 'reject')"
    >
        <span class="ti-sm ti ti-x"></span>
    </button>
</div>