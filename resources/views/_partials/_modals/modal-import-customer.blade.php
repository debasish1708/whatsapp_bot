<div class="modal fade" id="addNewCCModal" tabindex="-1" aria-labelledby="addNewCCModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="addNewCCModalLabel">{{ __('Import') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="importStudentForm" method="POST" action="{{ route('restaurant.customers.import') }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="import_file" class="form-label">{{ __("Choose CSV") }}</label>
            <div class="mb-2">
              <small class="text-muted">
                {{ __("Example: Download the sample CSV to see the required format.") }}
                <a href="{{ asset('samples/Example.csv') }}" download>{{ __("Download Sample CSV") }}</a>
              </small>
            </div>
            <input type="file" class="form-control" id="import_file" name="file" required accept=".csv, .xlsx, .xls">
          </div>
          <button type="submit" class="btn btn-primary me-3 data-submit btn-custom" id="importSubmitButton">{{ __('Upload') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
