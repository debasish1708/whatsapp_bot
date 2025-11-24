<div class="d-flex align-items-center gap-2">
{{-- "window.location.href='{{ route('school.psychological-support.edit', $psychological_support->id) }}'" --}}
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="showSosAleart('{{ $sos_alert->id }}')"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleSosEdit('{{ $sos_alert->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
  <button type="button" class="p-0 border-0 bg-transparent"
     onclick="deleteSosAlert('{{ route('school.sos-alerts.destroy', $sos_alert->id) }}')" title="DELETE">
     <i class="fas fa-trash-alt text-danger"></i>
   </button>
</div>
