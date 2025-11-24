<div class="d-flex align-items-center gap-2">
{{-- "window.location.href='{{ route('school.psychological-support.edit', $psychological_support->id) }}'" --}}
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="showPsychologicalDetails('{{ $psychological_support->id }}')"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handlePsychologicalSupportEdit('{{ $psychological_support->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
  <button type="button" class="p-0 border-0 bg-transparent"
     onclick="deletePsychologicalSupport('{{ route('school.psychological-support.destroy', $psychological_support->id) }}')" title="DELETE">
     <i class="fas fa-trash-alt text-danger"></i>
   </button>
</div>
