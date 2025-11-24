@extends('layouts.layoutMaster')

@section('title', __('Club & Activities - School Management'))

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
@endsection

@section('page-style')
<style>
  .dataTables_filter input {
    height: 35px !important;
  }
</style>

@endsection
@section('page-script')
@vite(['resources/assets/js/forms-pickers.js'])
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
                    title: "{{__('clubs.school.tour.clubs_title')}}",
                    text: "{{__('clubs.school.tour.clubs_text')}}",
                    element: '.card-datatable',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('clubs.school.tour.add_title')}}",
                    text: "{{__('clubs.school.tour.add_text')}}",
                    element: '.create-post-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('clubs.school.tour.search_title')}}",
                    text: "{{__('clubs.school.tour.search_text')}}",
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
    flatpickr("#flatpickr-datetime", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today"  // ⬅️ Disables past dates
    });
    // Status mapping
    const statusMap = {
      1: { title: '{{ __('Pending') }}', class: 'bg-label-warning' },
      2: { title: '{{ __('Active') }}', class: 'bg-label-success' },
      3: { title: '{{ __('Inactive') }}', class: 'bg-label-secondary' }
    };

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('school.club-activities.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description' },
        { data: 'meeting_time', name: 'meeting_time' },
        { data: 'location', name: 'location' },
        { data: 'contact_person', name: 'contact_person' },
        { data: 'contact_phone', name: 'contact_phone' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
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
      buttons: [
        {
          text: '<i class="ti ti-plus me-1"></i> {{ __('Add Club') }}',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Reset form
            $('#addNewUserForm')[0].reset();

            $('#addNewUserForm')
              .attr('action', '{{ route("school.club-activities.store") }}') // ✅ Laravel create route
              .find('input[name="_method"]').remove(); // Remove any hidden _method=PUT input

            // Reset modal labels
            $('#offcanvasAddUserLabel').text(@json(__('Add Club')));
            $('#updateLavel').text(@json(__('Add Club')));

            // Clear validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Show offcanvas
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddUser'));
            offcanvas.show();
          }
        }
      ],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
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
        }
    });

      // Optional: Adjust search input placeholder for clarity
      $('.dataTables_filter input').attr('placeholder', '{{ __('Search clubs...') }}');
  });

  // Listen for edit button click
  function handleEditclub(id) {
    let url = '{{ route("school.club-activities.edit", ":id") }}'.replace(':id', id);
    $.ajax({
      url: '/school/club-activity/' + id + '/edit',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // Fill the form fields with the fetched data
        $('#club-id').val(data.id);
        $('#club-name').val(data.name);
        $('#club-description').val(data.description);
        const formatted = data.meeting_time.slice(0, 16).replace('T', ' ');
        $('#flatpickr-datetime').val(formatted);
        $('#location').val(data.location);
        $('#contact_person').val(data.contact_person);
        $('#contact_phone').val(data.contact_phone);
        // Change form action to update route
        $('#addNewUserForm').attr('action', '{{ route("school.club-activities.update", ":id") }}'.replace(':id', id));
        // Add PUT method for update
        if ($('#addNewUserForm input[name="_method"]').length === 0) {
          $('#addNewUserForm').append('<input type="hidden" name="_method" value="PUT">');
        }
        // Change modal title
        $('#offcanvasAddUserLabel').text(@json(__('Edit Club')));
        $('#updateLavel').text(@json(__('Update')));
        $('#updateLavel').attr('data-loading-text', @json(__('Updating...')));
        // Show the modal
        $('#offcanvasAddUser').offcanvas('show');
      },
      error: function () {
        toastr.error('Failed to fetch club data.', 'Error');
      }
    });
  }

  // show the club details
  function showClubDetails(id) {
    $.ajax({
      url: '/school/club-activity/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // let  = data.meeting_time;
        const meetingTime = data.meeting_time.slice(0, 16).replace('T', ' ');

        // Populate each span/div
        // $('#clubName').text(data.name || '-');
        // $('#clubDescription').text(data.description || '-');
        // $('#clubMeetingTime').text(meetingTime || '-');
        // $('#clubLocation').text(data.location || '-');
        // $('#clubContactPerson').text(data.contact_person || '-');
        // $('#clubContactPhone').text(data.contact_phone || '-');
        // const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasViewClub'));
        // offcanvas.show();

        $('#modal-name').text(data.name || '-');
        $('#modal-description').text(data.description || '-');
        $('#modal-meeting-time').text(data.meeting_time || '-');
        $('#modal-location').text(data.location || '-');
        $('#modal-contact-person').text(data.contact_person || '-');
        $('#modal-contact-phone').text(data.contact_phone || '-');
        $('#created').text(data.created_at || '-');

        $("#modalCenter").modal('show');
      },
      error: function (error) {
        console.error('Error:', error);
        toastr.error('Failed to fetch club data.', 'Error');
      }
    });
  }

  function deleteClub(url){
      // let url=$(this).data('url');
      Swal.fire({
        text: "{{ __('Are you sure?') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes') }}",
        cancelButtonText: "{{ __('Cancel') }}",
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
                title: "{{ __('Success') }}",
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
        // show old data in the modal
        $('#club-name').val('{{ old('name') }}');
        $('#club-description').val('{{ old('description') }}');
        $('#flatpickr-datetime').val('{{ old('meeting_time') }}');
        $('#location').val('{{ old('location') }}');
        $('#contact_person').val('{{ old('contact_person') }}');
        $('#contact_phone').val('{{ old('contact_phone') }}');
        @if (old('club_id'))
          const oldId = "{{ old('club_id') }}";
          $('#club-id').val(oldId);
          $('#updateLavel').text('Update');

          $('#addNewUserForm').attr('action', '{{ route("school.club-activities.update", ":id") }}'.replace(':id', oldId));
          // Add PUT method for update
          if ($('#addNewUserForm input[name="_method"]').length === 0) {
            $('#addNewUserForm').append('<input type="hidden" name="_method" value="PUT">');
          }
          // Change modal title
          $('#offcanvasAddUserLabel').text('Edit Club');
          $('#updateLavel').text('Update');
          $('#updateLavel').attr('data-loading-text', 'Updating...');
        @endif
        // Change modal title
        $('#offcanvasAddUserLabel').text('Edit Club');
        // Show the modal
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
            <th>{{ __('ID') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Meeting Time') }}</th>
            <th>{{ __('Location') }}</th>
            <th>{{ __('Contact Person') }}</th>
            <th>{{ __('Contact Phone') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{ __('Add Club') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('school.club-activities.store') }}" method="post">
        @csrf
        <input type="hidden" name="club_id" id="club-id" value="">
        <div class="mb-6">
          <label class="form-label" for="club-name">{{ __('Name') }}</label>
          <input type="text" class="form-control" id="club-name" placeholder="{{ __('Enter club/activity name') }}" name="name" required/>
          <small class="text-danger">{{ $errors->first('name') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="club-description">{{ __('Description') }}</label>
          <textarea class="form-control" id="club-description" placeholder="{{ __('Enter description') }}" name="description" rows="3" required></textarea>
          <small class="text-danger">{{ $errors->first('description') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="flatpickr-datetime">{{ __('Meeting Time') }}</label>
          <input type="text" class="form-control" name="meeting_time" placeholder="{{ __('Enter Date and Time') }}" id="flatpickr-datetime" required />
          <small class="text-danger">{{ $errors->first('meeting_time') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="club-location">{{ __('Location') }}</label>
          <input type="text" class="form-control" id="location" name="location" placeholder="{{ __('Enter location') }}" id="club-location" required />
          <small class="text-danger">{{ $errors->first('location') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="club-contact-person">{{ __('Contact Person') }}</label>
          <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="{{ __('Enter contact person') }}" id="club-contact-person" required />
          <small class="text-danger">{{ $errors->first('contact_person') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="club-contact-phone">{{ __('Mob No') }}</label>
          <input type="text"
              class="form-control"
              id="contact_phone"
              name="contact_phone"
              placeholder="{{ __('Enter Mobile Number') }}"
              maxlength="14"
              oninput="this.value = this.value.replace(/[^0-9]/g, '')"
              required />
          <small class="text-danger">{{ $errors->first('contact_phone') }}</small>
        </div>
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom"
          data-loading-text="{{ __('Adding...') }}"
          onclick="this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.submit();">
          {{ __('Add Club') }}
          {{-- onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class='spinner-border spinner-border-sm'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }"> --}}
        </button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasViewClub" aria-labelledby="offcanvasViewClubLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasViewClubLabel" class="offcanvas-title">{{ __('View Club') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-0">
      <div class="card shadow-none border-0 mb-3 py-2">
        <div class="card-body p-0">
          <ul class="list-group list-group-flush border-0">
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-users me-2"></i>{{ __('Name') }}:</strong> <span id="clubName"></span>
            </li>
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-align-left me-2"></i>{{ __('Description') }}:</strong>
              <div id="clubDescription" style="white-space: pre-line;"></div>
            </li>
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-calendar-event me-2"></i>{{ __('Meeting Time') }}:</strong> <span id="clubMeetingTime"></span>
            </li>
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-map-pin me-2"></i>{{ __('Location') }}:</strong> <span id="clubLocation"></span>
            </li>
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-user me-2"></i>{{ __('Contact Person') }}:</strong> <span id="clubContactPerson"></span>
            </li>
            <li class="list-group-item border-0 py-1">
              <strong><i class="ti ti-phone me-2"></i>{{ __('Contact Phone') }}:</strong> <span id="clubContactPhone"></span>
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
                      <h5 class="modal-title" id="modalCenterTitle">{{ __('Club Details') }}</h5>
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
                                      <span class="fw-semibold me-1 m-2">{{__('Description')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-description"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Meeting Time') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-meeting-time"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-map-pin ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Location') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-location"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-user ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Contact Person') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-contact-person"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-phone ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Contact Phone') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-contact-phone"></span>
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
