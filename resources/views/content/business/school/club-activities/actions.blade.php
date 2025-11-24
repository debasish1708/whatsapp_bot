<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="showClubDetails('{{ $club->id }}')"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleEditclub('{{ $club->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
  <button type="button" class="p-0 border-0 bg-transparent"
     onclick="deleteClub('{{ route('school.club-activities.destroy', $club->id) }}')" title="DELETE">
     <i class="fas fa-trash-alt text-danger"></i>
   </button>

</div>
