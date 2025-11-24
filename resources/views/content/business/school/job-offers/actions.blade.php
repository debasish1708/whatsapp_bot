<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="showJobDetails('{{ $job_offer->id }}')"
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
    onclick="window.location.href='{{ route('school.job-offer.applicants', $job_offer->id) }}'"
    title="View Applicants">
    <i class="fas fa-file-alt text-muted"></i>
  </button>

  <!-- Delete Icon -->
  {{-- <button type="button" class="p-0 border-0 bg-transparent" data-bs-toggle="modal"
    data-bs-target="#deleteModal{{ $job_offer->id }}" title="Delete">
    <i class="fas fa-trash-alt text-danger"></i>
  </button> --}}

   <button type="button" class="p-0 border-0 bg-transparent"
     onclick="deleteJobOffer('{{ route('school.job-offer.destroy', $job_offer->id) }}')" title="DELETE">
     <i class="fas fa-trash-alt text-danger"></i>
   </button>

    <!-- Delete Confirmation Modal -->
    {{-- <div class="modal fade" id="deleteModal{{ $job_offer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $job_offer->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{ $job_offer->id }}">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Job Offer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                   <form action="{{ route('school.job-offer.destroy', $job_offer->id) }}" method="post" class="m-0 d-inline" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                  </form>
                </div>
            </div>
        </div>
    </div> --}}
</div>
