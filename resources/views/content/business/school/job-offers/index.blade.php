@extends('layouts.layoutMaster')

@section('title', __('Job Offer - School Management'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
  'resources/assets/vendor/libs/pickr/pickr-themes.scss'
])
 @vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
  'resources/assets/vendor/libs/pickr/pickr.js'
])
 @vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection

@section('page-style')
<style>
  .dataTables_filter input {
    height: 35px !important;
  }
</style>

@endsection
@section('page-script')
@vite([
  'resources/assets/js/forms-selects.js',
  'resources/assets/js/forms-tagify.js',
  'resources/assets/js/forms-typeahead.js'
])
@if(!$is_visited)
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const tour = new Shepherd.Tour({
                defaultStepOptions: {
                    scrollTo: false,
                    cancelIcon: { enabled: true }
                },
                useModalOverlay: true
            });
            const stepsInput = [
                {
                    title: "{{__('joboffers.school.tour.job_offers_title')}}",
                    text: "{{__('joboffers.school.tour.job_offers_text')}}",
                    element: '.card-datatable',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('joboffers.school.tour.add_title')}}",
                    text: "{{__('joboffers.school.tour.add_text')}}",
                    element: '.create-post-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('joboffers.school.tour.search_title')}}",
                    text: "{{__('joboffers.school.tour.search_text')}}",
                    element: '.dataTables_filter input',
                    position: 'right',
                    nextText: 'Continue',
                    finishText: 'Finish'
                },
            ];
            const steps = generateTourSteps(stepsInput);
            steps.forEach(step => tour.addStep(step));
            tour.start();
        });
    </script>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var flatpickrDate = document.querySelector('#flatpickr-date');
    const date = new Date();
    date.setDate(date.getDate() + 60);
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0'); // Months start from 0
    const dd = String(date.getDate()).padStart(2, '0');
    const defaultDate = `${yyyy}-${mm}-${dd}`;

    // Initialize Flatpickr with default date
    flatpickr("#flatpickr-date", {
      dateFormat: "Y-m-d",
      defaultDate: defaultDate
    });

    if (flatpickrDate) {
      flatpickrDate.flatpickr({
        monthSelectorType: 'static',
        minDate: 'today', // Set minimum date to today
      });
    }

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('school.job-offer.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __("ID") }}' },
        { data: 'position', name: 'position', title: '{{ __("position") }}' },
        { data: 'description', name: 'description', title: '{{ __("description") }}' },
        { data: 'salary', name: 'salary', title: '{{ __("salary") }}' },
        { data: 'location', name: 'location', title: '{{ __("location") }}' },
        { data: 'expiry_date', name: 'expiry_date', title: '{{ __("Expiry date") }}' },
        {
          data: 'status',
          name: 'status',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            let statusMap = {
              'active': { title: '{{ __("Active") }}', class: 'bg-label-success' },
              'inactive': { title: '{{ __("Inactive") }}', class: 'bg-label-warning' },
              'expired': { title: '{{ __("Expired") }}', class: 'bg-label-danger' }
            };
            let key = (data || '').toString().toLowerCase();
            let statusObj = statusMap[key];
            if (statusObj) {
              return `<span class="badge ${statusObj.class}">${statusObj.title}</span>`;
            } else {
              return `<span class="badge bg-label-secondary">${data}</span>`;
            }
          }
        },
        { data: 'applicants', name: 'applicants', title: '{{ __("Applicants") }}', className: 'text-center' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __("Action") }}' },
      ],
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"' +
              '<"me-2"f>' +
              'B' +
            '>' +
          '>' +
        '>t' +
        '<"row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        "emptyTable": "{{ __('No data available in table') }}",
        "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
        "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
        "infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
        "lengthMenu": "{{ __('Show _MENU_ entries') }}",
        "loadingRecords": "{{ __('Loading...') }}",
        "processing": "{{ __('Processing...') }}",
        "search": "{{ __('Search:') }}",
        "zeroRecords": "{{ __('No matching records found') }}",
        "paginate": {
          "first": "{{ __('First') }}",
          "last": "{{ __('Last') }}",
          "next": "{{ __('Next') }}",
          "previous": "{{ __('Previous') }}"
        }
      },
      buttons: [
        {
          text: '<i class="ti ti-plus me-1"></i> {{ __("Add Job Offer") }}',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Reset form
            $('#addJobOfferForm')[0].reset();
            $('#job_offer_id').val('');
            $('#addJobOfferForm')
              .attr('action', '{{ route("school.job-offer.store") }}')
              .find('input[name="_method"]').remove(); // Remove PUT method if present
            $('#addJobOfferModalLabel').text('{{ __("Add Job Offer") }}');
            $('#updateLavel').text('{{ __("Add Job Offer") }}').attr('data-loading-text', '{{ __("Adding...") }}');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            // Show offcanvas
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
            offcanvas.show();
          }
        }
      ],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip()
        });

        // Optional: Adjust search input placeholder for clarity
        $('.dataTables_filter input').attr('placeholder', '{{ __("Search Jobs...") }}');
        $('#status').select2({
          dropdownParent: $('#addJobOfferModal'),
          placeholder: '{{ __("Select type") }}',
          allowClear: true,
          width: '100%'
        });

      });

    // Listen for edit button click
  function handleEditJobOffer(id) {
    $.ajax({
      url: '/school/job-offer/' + id + '/edit',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // Fill form fields
        console.log(data);
        $('#job_offer_id').val(data.id);
        $('#position').val(data.position);
        $('#description').val(data.description);
        $('#location').val(data.location);
        $('#salary').val(data.salary);
        $('#contact_email').val(data.contact_email);
        $('#contact_number').val(data.contact_number);
        const formatted = data.expiry_date?.split('T')[0]; // "2025-08-19"
        $('#flatpickr-date').val(formatted);
        $('#status').val(data.status).trigger('change'); // ✅ This is the correct way

        // Update form action to PUT
        $('#addJobOfferForm').attr('action', '/school/job-offer/' + id);
        if ($('#addJobOfferForm input[name="_method"]').length === 0) {
          $('#addJobOfferForm').append('<input type="hidden" name="_method" value="PUT">');
        }

        // Update modal title and button
        $('#addJobOfferModalLabel').text('{{ __("Edit Job Offer") }}');
        $('#updateLavel').text('{{ __("Update") }}');
        $('#updateLavel').attr('data-loading-text', '{{ __("Updating...") }}');

        // Show modal
        let jobOffcanvas = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
        jobOffcanvas.show();
      },
      error: function (xhr) {
        console.error('Failed to load job offer:', xhr);
        toastr.error('Could not fetch job data.', 'Error');
      }
    });
  }

  // View Job Offer Modal Handler
  function showJobDetails(id) {
    $.ajax({
      url: '/school/job-offer/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // Fill modal fields
        console.log(data);
        $('#modal-position').text(data.position || '');
        $('#modal-description').text(data.description || '');
        $('#modal-location').text(data.location || '');
        $('#modal-salary').text(data.salary ? data.salary + ' CZK' : '');
        $('#modal-contact-email').text(data.contact_email || '');
        $('#modal-contact-mobile-number').text(data.contact_number || data.contact_phone || '');
        const formatted = data.expiry_date?.split('T')[0] || data.expiry_date || '';
        $('#modal-expiry-date').text(formatted);
        $('#created').text(data.created_at?.split('T')[0] || '');
        const statusMap = {
          'active': { title: '{{ __("Active") }}', class: 'bg-label-success' },
          'inactive': { title: '{{ __("Inactive") }}', class: 'bg-label-warning' },
          'expired': { title: '{{ __("Expired") }}', class: 'bg-label-danger' }
        };
        const key = (data.status || '').toString().toLowerCase();
        const statusObj = statusMap[key];
        let statusBadge = statusObj
          ? `<span class="badge ${statusObj.class}">${statusObj.title}</span>`
          : `<span class="badge bg-label-secondary">${data.status || 'Unknown'}</span>`;
        $('#modal-status').html(statusBadge);
        $('#modalCenter').modal('show');
      },
      error: function (error) {
        toastr.error('Failed to fetch job offer data.', 'Error');
      }
    });
  }

  function deleteJobOffer(url){
      // let url=$(this).data('url');
      Swal.fire({
        text: "{{ __('Are you sure?') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
          $.ajax({
            method: 'DELETE',
            url: url,
            data: {
              _token: "{{ csrf_token() }}"
            },
            success: function (result) {
              Swal.fire({
                icon: 'success',
                title:"{{ __('Success') }}",
                text: result.message,
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
              });
              $('.datatables-users').DataTable().ajax.reload(null, false);
            },
            error: function (error) {
              Swal.fire({
                icon: 'error',
                // title:"Some error occured",
                title: error.responseJSON.message,
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
              });
            }
          })
        }
      })
    }
</script>

@if ($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const addJobOfferModal = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
      const form = $('#addJobOfferForm');
      const modalLabel = $('#addJobOfferModalLabel');
      const submitButton = $('#updateLavel');

      // Repopulate form fields with old data
      $('#job_offer_id').val('{{ old('job_offer_id') }}');
      $('#position').val('{{ old('position') }}');
      $('#description').val('{{ old('description') }}');
      $('#location').val('{{ old('location') }}');
      $('#salary').val('{{ old('salary') }}');
      $('#contact_email').val('{{ old('contact_email') }}');
      $('#contact_number').val('{{ old('contact_number') }}');
      $('#flatpickr-date').val('{{ old('expiry_date') }}');
      $('#status').val('{{ old('status') }}').trigger('change');

      // Handle multiple select for job types
      let jobTypes = @json(old('job_type'));
      if (jobTypes) {
        $('#job_type').val(jobTypes).trigger('change');
      }

      // Check if it was an update or create form that failed validation
      @if (old('job_offer_id'))
        // Edit mode
        form.attr('action', '{{ route("school.job-offer.update", old("job_offer_id")) }}');
        if (form.find('input[name="_method"]').length === 0) {
          form.append('<input type="hidden" name="_method" value="PUT">');
        }
        modalLabel.text('{{ __("Edit Job Offer") }}');
        submitButton.text('{{ __("Update") }}');
        submitButton.attr('data-loading-text', '{{ __("Updating...") }}');
      @else
        // Add mode
        form.attr('action', '{{ route("school.job-offer.store") }}');
        form.find('input[name="_method"]').remove();
        modalLabel.text('{{ __("Add Job Offer") }}');
        submitButton.text('{{ __("Add Job Offer") }}');
        submitButton.attr('data-loading-text', '{{ __("Adding...") }}');
      @endif

      // Show the modal with the pre-filled data and validation errors
      addJobOfferModal.show();
    });
  </script>
@endif

@endsection

@section('content')

  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // show old data in the modal
        // $('#job_offer-name').val('{{ old('name') }}');
        // $('#job_offer-description').val('{{ old('description') }}');
        // $('#flatpickr-date').val('{{ old('meeting_time') }}');
        // $('#location').val('{{ old('location') }}');
        // $('#contact_person').val('{{ old('contact_person') }}');
        // $('#contact_phone').val('{{ old('contact_phone') }}');

        // // Change modal title
        // $('#offcanvasAddUserLabel').text('Edit Club');
        // $('#updateLavel').text('Update');
        // // Show the modal
        // $('#offcanvasAddUser').offcanvas('show');
      });
    </script>
  @endif
<!-- Users List Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('position') }}</th>
          <th>{{ __('description') }}</th>
          <th>{{ __('salary') }}</th>
          <th>{{ __('location') }}</th>
          <th>{{ __('Expiry date') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Applicants') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addJobOfferModal" aria-labelledby="addJobOfferModalLabel">
    <div class="offcanvas-header">
      <h5 id="addJobOfferModalLabel" class="offcanvas-title">{{ __('Add Job Offer') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-user pt-0" id="addJobOfferForm" action="{{ route('school.job-offer.store') }}" method="POST">
        @csrf
        <input type="hidden" name="job_offer_id" id="job_offer_id">
        <div class="mb-3">
          <label class="form-label" for="position">{{ __('position') }}</label>
          <input type="text" class="form-control" id="position" name="position" placeholder="{{ __('e.g. 2nd grade English Teacher') }}" required>
          <small class="text-danger">{{ $errors->first('position') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="description">{{ __('description') }}</label>
          <textarea class="form-control" id="description" name="description" rows="3" maxlength="400" placeholder="{{ __('Enter job description (max 400 chars)') }}"></textarea>
          <small class="text-danger">{{ $errors->first('description') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="location">{{ __('location') }}</label>
          <input type="text" class="form-control" id="location" name="location" placeholder="{{ __('School Address') }}" required>
          <small class="text-danger">{{ $errors->first('location') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="salary">{{ __('salary') }}</label>
          <input type="text" class="form-control" id="salary" name="salary" maxlength="5" oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')"
               placeholder="{{ __('400 czk') }}" required>
          <small class="text-danger">{{ $errors->first('salary') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="contact">{{ __('Contact Email') }}</label>
          <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="{{ __('Enter email address') }}" required>
          <small class="text-danger">{{ $errors->first('contact_email') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="contact">{{ __('Contact mobile number') }}</label>
          <input type="tel" class="form-control" id="contact_number" name="contact_number" placeholder="{{ __('Enter mobile number') }}"
              pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
          <small class="text-danger">{{ $errors->first('contact_number') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="flatpickr-date">{{ __('Expiry date') }}</label>
          <input type="text" class="form-control" id="flatpickr-date" name="expiry_date" placeholder="{{ __('Select expiry date') }}">
          <small class="text-muted">{{ __('default date') }}</small>
          <small class="text-danger">{{ $errors->first('expiry_date') }}</small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="status">{{ __('Status') }}</label>
          <select class="select2 form-select form-select" data-style="btn-default" id="status" name="status" required>
            <option value="">{{ __('Select Status') }}</option>
              @foreach (\App\Enums\SchoolJobOffer::cases() as $status)
                  <option value="{{ $status->value }}"
                      {{ old('status') === $status->value ? 'selected' : '' }}>
                      {{ Illuminate\Support\Str::headline(__($status->name)) }}
                  </option>
              @endforeach
          </select>
          <small class="text-danger">{{ $errors->first('status') }}</small>
        </div>

        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom"
          data-loading-text="{{ __('Adding...') }}"
          onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }">
          {{ __('Add Job Offer') }}
        </button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal for Viewing Job Offer Details -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">{{ __('View Job Offer') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-unstyled">
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-briefcase ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('position')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-position"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-align-left ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('description')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-description"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-map-pin ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('location')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-location"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-currency-dollar ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('salary')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-salary"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-mail ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Contact Email')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-contact-email"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-phone ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Contact mobile number')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-contact-mobile-number"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-calendar-event ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Expiry date')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-expiry-date"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-tag ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Status')}}:</span>
              </div>
              <span class="text-break m-2"><span id="modal-status" class="badge bg-label-secondary"></span></span>
            </div>
          </li>
          <li class="mb-2 pt-1">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-calendar ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{ __('Created at') }}:</span>
              </div>
              <span class="text-break m-2" id="created"></span>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection
