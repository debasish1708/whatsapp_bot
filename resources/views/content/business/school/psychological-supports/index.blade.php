@extends('layouts.layoutMaster')

@section('title', __('Psychological Support'))

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
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@vite(['resources/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.scss'])
 @vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
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
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@vite(['resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js'])
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-style')
<style>
  .dataTables_filter input {
    height: 35px !important;
  }
  .office-hours-card {
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    background: #fff;
    padding: 1.5rem 1.5rem 1rem 1.5rem;
    margin-bottom: 2rem;
    border: none;
  }
  .office-hours-title {
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 1.2rem;
    color: #333;
  }
  .office-hours-repeater-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem 0.75rem 0.5rem 0.75rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    transition: box-shadow 0.2s;
    border: 1px solid #e9ecef;
  }
  .office-hours-repeater-item:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
  }
  .office-hours-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
  }
  .office-hours-select,
  .office-hours-time {
    border-radius: 6px;
    min-height: 38px;
    font-size: 1rem;
  }
  .office-hours-delete-btn {
    font-size: 1.3rem;
    color: #e74c3c;
    background: transparent;
    border: none;
    transition: background 0.2s, color 0.2s;
    border-radius: 50%;
    padding: 0.3rem 0.6rem;
  }
  .office-hours-delete-btn:hover {
    background: #ffeaea;
    color: #c0392b;
  }
  .office-hours-add-btn {
    border-radius: 20px;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
    margin-top: 0.5rem;
    background: #fff;
    border: 1px solid #ff69b4;
    color: #ff69b4;
    transition: background 0.2s, color 0.2s;
  }
  .office-hours-add-btn:hover {
    background: #ff69b4;
    color: #fff;
  }
  /* Increase the width of the right offcanvas */
  .offcanvas.offcanvas-end {
    width: 600px;
    max-width: 85vw;
  }
  @media (max-width: 767.98px) {
    .offcanvas.offcanvas-end {
      width: 100vw;
    }
  }
</style>

@endsection
@section('page-script')
@vite([
  'resources/assets/js/forms-selects.js',
  'resources/assets/js/forms-tagify.js',
  'resources/assets/js/forms-typeahead.js',
  'resources/js/psychological-support.js'
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
                    title: "{{__('psychological_supports.school.tour.supports_title')}}",
                    text: "{{__('psychological_supports.school.tour.supports_text')}}",
                    element: '.card-datatable',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('psychological_supports.school.tour.add_title')}}",
                    text: "{{__('psychological_supports.school.tour.add_text')}}",
                    element: '.create-post-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('psychological_supports.school.tour.search_title')}}",
                    text: "{{__('psychological_supports.school.tour.search_text')}}",
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
    const dayColors = @json($dayColors ?? []);
    const timeColors = @json($timeColors ?? []);
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('school.psychological-support.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __('ID') }}' },
        { data: 'name', name: 'name', title: '{{ __('Name') }}' },
        { data: 'mobile_number', name: 'mobile_number', title: '{{ __('Contact No') }}' },
        {
          data: 'office_hours',
          name: 'office_hours',
          orderable: false,
          searchable: false,
          title: '{{ __('Office Hours') }}',
          render: function (data, type, row) {
            if (!data || data === 'N/A') return '<span class="text-muted">N/A</span>';

            // Handle both newline and <br> separated data
            const lines = data.split(/\n|<br\s*\/?>/i).filter(line => line.trim());

            const html = lines.map(line => {
              line = line.trim();
              if (!line) return '';

              // Match pattern: "Day: Time"
              const match = line.match(/^(\w+):\s*(.*)$/i);
              if (!match) {
                // If no colon found, try to parse differently
                // Handle cases like "Sunday: 10:00 AM - 4:00 PM" or plain text
                const words = line.split(/\s+/);
                if (words.length > 1) {
                  const possibleDay = words[0].replace(':', '').toLowerCase();
                  if (dayColors[possibleDay]) {
                    const timepart = words.slice(1).join(' ');
                    const badgeClass = dayColors[possibleDay];
                    const timeClass = timeColors[possibleDay];
                    return `<div class="mb-1">
                              <span class="badge ${badgeClass} me-2">${words[0].replace(':', '')}</span>
                              <span class="${timeClass}">${timepart}</span>
                            </div>`;
                  }
                }
                return `<div class="mb-1 text-muted">${line}</div>`;
              }

              const day = match[1];
              const time = match[2];
              const badgeClass = dayColors[day.toLowerCase()] || 'bg-label-secondary';
              const timeClass = timeColors[day.toLowerCase()] || 'text-dark';

              return `<div class="mb-1">
                        <span class="badge ${badgeClass} me-2">${day}</span>
                        <span class="${timeClass}">${time}</span>
                      </div>`;
            }).filter(html => html).join('');

            return html || '<span class="text-muted">{{ __('No office hours') }}</span>';
          }
        },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __('Action') }}' }
      ],
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"' +
              '<"me-2"f>B' +
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
          text: '<i class="ti ti-plus me-1"></i> {{ __('Add Psychological Support') }}',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Reset the form
            $('#addNewUserForm')[0].reset();
            $('#psychological_support-id').val('');
            // Remove any _method input (for edit)
            $('#addNewUserForm').attr('action', '{{ route("school.psychological-support.store") }}');
            $('#addNewUserForm').find('input[name="_method"]').remove();
            // Fully reset the office hours repeater and dropdowns
            if (typeof window.resetOfficeHoursRepeater === 'function') window.resetOfficeHoursRepeater();
            $('#updateLavel').text('{{ __('Add') }}');
            $('#updateLavel').attr('data-loading-text', @json(__('Submitting...')));
            // Set modal title and button
            $('#offcanvasAddUserLabel').text('{{ __('Add Psychological Support') }}');
            $('#updateLavel').text('{{ __('Submit') }}');
            // Clear all validation errors and highlights
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.text-danger').text('');
            // Show the modal
            $('#offcanvasAddUser').offcanvas('show');
          }
        }
      ],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip()
    });

    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', '{{ __('Search Psychological Support...') }}');
  });

  // Listen for edit button click
  function handlePsychologicalSupportEdit(id) {
    // Clear all validation errors and highlights
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.text-danger').text('');
    let url = '{{ route("school.psychological-support.edit", ":id") }}'.replace(':id', id);
    $.ajax({
      url: '/school/psychological-support/' + id + '/edit',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // Fill the form fields with the fetched data
        $('#psychological_support-id').val(data.id);
        $('#psychological_support-name').val(data.name);
        $('#mobile-number').val(data.mobile_number);
        console.log(data);

        // Change form action to update route
        $('#addNewUserForm').attr('action', '{{ route("school.psychological-support.update", ":id") }}'.replace(':id', id));
        // Add PUT method for update
        if ($('#addNewUserForm input[name="_method"]').length === 0) {
          $('#addNewUserForm').append('<input type="hidden" name="_method" value="PUT">');
        }
        // Change modal title
        $('#offcanvasAddUserLabel').text('{{ __('Edit Psychological Support') }}');
        $('#updateLavel').text('{{ __('Update') }}');
        $('#updateLavel').attr('data-loading-text', '{{ __('Updating...') }}');

        // Use the new JS function to populate the repeater
        populateOfficeHoursRepeater(data.office_hours);

        // Show the modal
        $('#offcanvasAddUser').offcanvas('show');
      },
      error: function () {
        toastr.error('{{ __('Failed to fetch psychological_support data.') }}', '{{ __('Error') }}');
      }
    });
  }

  function showPsychologicalDetails(id) {
    $.ajax({
      url: '/school/psychological-support/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // $('#psName').text(data.name);
        // $('#psMobile').text(data.mobile_number);
        $('#modal-name').text(data.name);
        $('#modal-contact-number').text(data.mobile_number);
        $('#created').text(data.created_at);

        // const $hoursList = $('#psOfficeHours');
        const $hoursList = $('#modal-office-hours');
        $hoursList.empty();

        if (data.office_hours && data.office_hours.length > 0) {
          data.office_hours.forEach(hour => {
            let start = hour.start_time;
            let end = hour.end_time;
            try {
              start = new Date('1970-01-01T' + hour.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
              end = new Date('1970-01-01T' + hour.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } catch (e) {}

            // $hoursList.append(`<li>${hour.day}: ${start} - ${end}</li>`);
            // Use lowercase for lookup
            const dayKey = hour.day.toLowerCase();
            const badgeClass = dayColors[dayKey] || 'bg-label-secondary';
            const timeClass = timeColors[dayKey] || 'text-dark';

            $hoursList.append(
              `<li class="mb-1 d-flex align-items-center">
                <span class="badge ${badgeClass} me-2">${hour.day}</span>
                <span class="${timeClass}">${start} - ${end}</span>
              </li>`
            );
          });
        } else {
          $hoursList.append('<li>{{ __('No office hours available.') }}</li>');
        }

        // Show offcanvas
        // const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasViewPsychologicalSupport'));
        // offcanvas.show();
        $("#modalCenter").modal('show');
      },
      error: function (error) {
        console.error('Error:', error);
        toastr.error('{{ __('Failed to fetch psychological support data.') }}', '{{ __('Error') }}');
      }
    });
  }

  function deletePsychologicalSupport(url){
      // let url=$(this).data('url');
      Swal.fire({
        text: "{{ __('Are you sure?') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes') }}",
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false,
        cancelButtonText: "{{ __('Cancel') }}"
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
@endsection

@section('content')

 @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const $form = $('#addNewUserForm');
        $form[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Fill in old values
        $('#psychological_support-name').val(@json(old('name')));
        $('#mobile_number').val(@json(old('mobile_number')));

        // Office hours repeater: set global variable for offcanvas event
        @if(is_array(old('office_hours')))
          window.currentOfficeHoursData = @json(old('office_hours'));
        @else
          window.currentOfficeHoursData = null;
        @endif

        // Determine if edit or add
        @if(old('psychological_support_id'))
          // Edit mode
          $('#psychological_support-id').val(@json(old('psychological_support_id')));
          $form.attr('action', '{{ route("school.psychological-support.update", ":id") }}'.replace(':id', @json(old('psychological_support_id'))));
          if ($form.find('input[name="_method"]').length === 0) {
            $form.append('<input type="hidden" name="_method" value="PUT">');
          }
          $('#offcanvasAddUserLabel').text(@json(__('Edit Psychological Support')));
          $('#updateLavel').text(@json(__('Update')));
          $('#updateLavel').attr('data-loading-text', @json(__('Updating...')));
        @else
          // Add mode
          $('#psychological_support-id').val('');
          $form.attr('action', '{{ route("school.psychological-support.store") }}');
          $form.find('input[name="_method"]').remove();
          $('#offcanvasAddUserLabel').text(@json(__('Add Psychological Support')));
          $('#updateLavel').text(@json(__('Submit')));
          $('#updateLavel').attr('data-loading-text', @json(__('Submitting...')));
        @endif

        // Show the offcanvas after repeater is set up
        $('#offcanvasAddUser').offcanvas('show');
      });
    </script>
  @endif

<!-- Users List Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Contact No</th>
          <th>Office Hours</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{ __('Add Psychological Support') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('school.psychological-support.store') }}" method="post">
        @csrf
        <input type="hidden" name="psychological_support_id" id="psychological_support-id" value="">
        <div class="mb-6">
          <label class="form-label" for="psychological_support-name">{{ __('Name') }}</label>
          <input type="text" class="form-control" id="psychological_support-name" placeholder="{{ __('Enter psychological support name') }}" name="name" required/>
          <small class="text-danger">{{ $errors->first('name') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="psychological_support-contact-phone">{{ __('Mob No') }}</label>
          <input type="text" class="form-control" id="mobile-number" name="mobile_number" oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')" maxlength="15" placeholder="{{ __('Enter Mobile Number') }}" required>
          <small class="text-danger">{{ $errors->first('mobile_number') }}</small>
        </div>

        <!-- Form Repeater -->
        <div class="mb-4">
          <div class="office-hours-card">
            <div class="office-hours-title">{{ __('Office Hours') }}</div>
            <div class="form-repeater" id="office-hours-form">
              <div data-repeater-list="office_hours">
                <div data-repeater-item class="office-hours-repeater-item">
                  <div class="row g-3 align-items-center">
                    <!-- Day -->
                    <div class="col-lg-4 col-12">
                      <label class="office-hours-label">{{ __('Day') }}</label>
                      <select class="form-select office-hours-select day-select" name="day" required>
                        <option value="">{{ __('Select Day') }}</option>
                        @foreach(\App\Enums\SchoolPsychologicalOfficeHour::cases() as $day)
                          <option value="{{ $day->value }}" {{ (old('day', $selectedDay ?? null) === $day->value) ? 'selected' : '' }}>
                            {{ $day->value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    {{-- <div class="row g-3 align-items-end office-hours-row"> --}}
                      <!-- Start Time -->
                      <div class="col-md-4 col-12">
                        <label class="office-hours-label">{{ __('Start Time') }}</label>
                        <input type="text" class="form-control office-hours-time flatpickr-time" name="start_time" placeholder="{{ __('10:00') }}" required />
                      </div>

                      <!-- End Time -->
                      <div class="col-md-4 col-12">
                        <label class="office-hours-label">{{ __('End Time') }}</label>
                        <input type="text" class="form-control office-hours-time flatpickr-time" name="end_time" placeholder="{{ __('16:00') }}" required />
                        <small class="text-danger time-error"></small>
                      </div>

                      <!-- Delete Button -->
                      <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-link p-1 m-2 text-danger" data-repeater-delete title="{{ __('Delete') }}">
                          <i class="ti ti-trash"></i>
                        </button>
                      </div>
                    {{-- </div> --}}
                  </div>
                </div>
              </div>
              <!-- Add Button -->
              <div class="text-end">
                <button type="button" class="office-hours-add-btn" data-repeater-create>
                  <i class="ti ti-plus"></i> {{ __('Add') }}
                </button>
              </div>
            </div>
          </div>
        </div>
{{--
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">Submit</button> --}}
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom"
          data-loading-text="{{ __('Submitting...') }}"
          onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }">
          {{ __('Submit') }}
        </button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasViewPsychologicalSupport" aria-labelledby="offcanvasViewPsychologicalSupportLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasViewPsychologicalSupportLabel" class="offcanvas-title">{{ __('View Psychological Support') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-0">
    <div class="card shadow-none border-0 mb-3 py-2">
      <div class="card-body p-0">
        <ul class="list-group list-group-flush border-0">
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-user me-2"></i>{{ __('Name') }}:</strong> <span id="psName"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-phone me-2"></i>{{ __('Mobile No') }}:</strong> <span id="psMobile"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-clock me-2"></i>{{ __('Office Hours') }}:</strong>
            <ul id="psOfficeHours" class="mb-0 ps-4"></ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-md-6">
    <div class="mt-4">
        <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="modalCenterTitle">{{ __('Support Details') }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <ul class="list-unstyled">
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-bell ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Name')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-name"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-align-left ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Contact Number') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-contact-number"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Office Hours') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-office-hours"></span>
                              </div>
                          </li>
                          <li class="mb-2 pt-1">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Added on') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="created"></span>
                              </div>
                          </li>
                      </ul>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
