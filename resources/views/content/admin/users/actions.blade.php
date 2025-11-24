<div class="d-flex align-items-center gap-3">
    <a href="{{route('admin.users.show', $user->id)}}" class="btn btn-sm btn-icon" title="Show">
        <span class="ti-sm ti ti-eye"></span>
    </a>

    {{-- <button type="button"
            class="btn btn-sm btn-icon"
            title="DELETE"
            onclick="deleteUser('{{ route('admin.users.destroy', $user->id) }}')" title="DELETE">
     <span class="ti-sm ti ti-trash text-danger"></span>
   </button> --}}
</div>