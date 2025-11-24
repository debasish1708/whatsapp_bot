<div class="d-flex align-items-center gap-3">
    <a href="{{route('schools.show', $school->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>
    @if($school->is_profile_completed && $school->user->status == 'pending')
        <button 
            type="button" 
            class="btn btn-sm btn-icon" 
            title="Approve" 
            onclick="handleStatusButtons('{{route('schools.approve', $school->id)}}', 'approve')"
        >
            <span class="ti-sm ti ti-check text-success"></span>
        </button>
        <button 
            type="button" 
            class="btn btn-sm btn-icon" 
            title="Reject"
            onclick="handleStatusButtons('{{route('schools.reject', $school->id)}}', 'reject')"
        >
            <span class="ti-sm ti ti-x text-warning"></span>
        </button>
    @endif
    <button type="button"
            class="btn btn-sm btn-icon"
            title="DELETE"
            onclick="handleStatusButtons('{{route('schools.destroy', $school->id)}}', 'delete')">
     <span class="ti-sm ti ti-trash text-danger"></span>
   </button>
</div>