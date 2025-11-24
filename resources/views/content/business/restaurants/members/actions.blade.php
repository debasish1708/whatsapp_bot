
<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleViewMember('{{ $member->id }}')"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleEditMember('{{ $member->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
 <button type="button" class="p-0 border-0 bg-transparent"
    onclick="deleteMember('{{ route('members.destroy', $member->id) }}')" title="DELETE">
    <i class="fas fa-trash-alt text-danger"></i>
  </button>

  <!-- Chat Icon -->
  <button type="button" class="p-0 border-0 bg-transparent"
    onclick="handleChatMember('{{ $member->user->id }}')" title="Chat">
    <i class="fas fa-comments text-success"></i>
  </button>
  
</div>
