<div class="d-flex align-items-center gap-2">

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" onclick="handleEditJobOffer('{{ $user->id }}')"
    title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
  <button type="button" class="p-0 border-0 bg-transparent" data-bs-toggle="modal"
    onclick="deleteItem('{{ route('restaurant.customers.destroy',$user->user->id) }}')"  title="Delete">
    <i class="fas fa-trash-alt text-danger"></i>
  </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal{{ $user->user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{ $user->user->id }}">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Job Offer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('restaurant.customers.destroy', $user->user->id) }}" method="post" class="m-0 d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
