
<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleViewJobOffer('{{ $job_offer->id }}')"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>
  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleEditJobOffer('{{ $job_offer->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Applicants Icon Button -->
  <button type="button" class="p-0 border-0 bg-transparent"
    onclick="window.location.href='{{ route('job-offers.applicants', $job_offer->id) }}'"
    title="View Applicants">
    <i class="fas fa-file-alt text-muted"></i>
  </button>

  <!-- Delete Icon -->
 <button type="button" class="p-0 border-0 bg-transparent"
    onclick="deleteJobOffer('{{ route('job-offers.destroy', $job_offer->id) }}')" title="DELETE">
    <i class="fas fa-trash-alt text-danger"></i>
  </button>
</div>
