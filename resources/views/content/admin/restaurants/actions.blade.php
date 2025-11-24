<div class="d-flex align-items-center gap-3">
    <a href="{{route('restaurants.show', $restaurant->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>
    @if($restaurant->is_profile_completed && $restaurant->user->status=='pending')
        <button 
            type="button" 
            class="btn btn-sm btn-icon" 
            title="Approve" 
            onclick="handleStatusButtons('{{route('restaurants.approve', $restaurant->id)}}', 'approve')"
        >
            <span class="ti-sm ti ti-check text-success"></span>
        </button>
        <button 
            type="button" 
            class="btn btn-sm btn-icon text-warning" 
            title="Reject"
            onclick="handleStatusButtons('{{route('restaurants.reject', $restaurant->id)}}', 'reject')"
        >
            <span class="ti-sm ti ti-x"></span>
        </button>
    @endif
    <button type="button"
            class="btn btn-sm btn-icon"
            title="DELETE"
            onclick="handleStatusButtons('{{route('restaurants.destroy', $restaurant->id)}}', 'delete')" title="DELETE">
     <span class="ti-sm ti ti-trash text-danger"></span>
   </button>
</div>
