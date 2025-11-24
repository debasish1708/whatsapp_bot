@extends('layouts.layoutMaster')

@section('title', __('Announcements - Restaurant Management'))

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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var flatpickrDate = document.querySelector('#flatpickr-date');
    var flatpickrDate1 = document.querySelector('#flatpickr-date1');
    if (flatpickrDate) {
      flatpickrDate.flatpickr({
        monthSelectorType: 'static',
        minDate: 'today', // Set minimum date to today
        onChange: function(selectedDates, dateStr) {
          // Update the minimum date of the end date picker
          if (flatpickrDate1._flatpickr) {
            flatpickrDate1._flatpickr.set('minDate', dateStr);
          }
        }
      });
    }
    if (flatpickrDate1) {
      flatpickrDate1.flatpickr({
        monthSelectorType: 'static',
        minDate: 'today' // Initial minimum date
      });
    }

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('announcements.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description' },
        { data: 'start_date', name: 'start_date' },
        { data: 'end_date', name: 'end_date' },
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
        // Export button is hidden for now
        // {
        //   text: '<i class="ti ti-download me-1"></i> Export',
        //   className: 'btn btn-light me-2 export-btn',
        //   extend: 'collection',
        //   buttons: [
        //     {
        //       extend: 'print',
        //       text: '<i class="ti ti-printer me-1"></i> Print',
        //       className: 'dropdown-item',
        //       exportOptions: {
        //         modifier: { page: 'all' },
        //         columns: [0, 1, 2, 3, 4, 5, 6]
        //       }
        //     },
        //     {
        //       extend: 'csv',
        //       text: '<i class="ti ti-file-text me-1"></i> CSV',
        //       className: 'dropdown-item',
        //       exportOptions: {
        //         modifier: { page: 'all' },
        //         columns: [0, 1, 2, 3, 4, 5, 6]
        //       }
        //     },
        //     {
        //       extend: 'excel',
        //       text: '<i class="ti ti-file-spreadsheet me-1"></i> Excel',
        //       className: 'dropdown-item',
        //       exportOptions: {
        //         modifier: { page: 'all' },
        //         columns: [0, 1, 2, 3, 4, 5, 6]
        //       }
        //     },
        //     {
        //       extend: 'pdf',
        //       text: '<i class="ti ti-file-description me-1"></i> PDF',
        //       className: 'dropdown-item',
        //       exportOptions: {
        //         modifier: { page: 'all' },
        //         columns: [0, 1, 2, 3, 4, 5, 6]
        //       }
        //     }
        //   ]
        // },
        {
          text: '<i class="ti ti-plus me-1"></i> {{__('Add Announcement')}}',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Reset form fields
            $('#addNewUserForm')[0].reset();
            // Initialize/Reset Select2 for type
            $('#announcement-type').select2({
              dropdownParent: $('#offcanvasAddUser'),
              placeholder: '{{ __("Select type") }}',
              allowClear: true,
              width: '100%'
            }).val('').trigger('change');
            $('#offcanvasAddUser').offcanvas('show');
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
        $('.dataTables_filter input').attr('placeholder', '{{ __("Search announcements...") }}');
      });

  // Listen for edit button click
  function handleEditAnnouncement(id) {

    let url = '{{ route("announcements.edit", ":id") }}'.replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        console.log(data);
        // Fill the form fields with the fetched data
        $('#announcement-id').val(data.id);
        $('#announcement-title').val(data.title);
        $('#announcement-description').val(data.description);
        $('#flatpickr-date').val(data.start_date);
        $('#flatpickr-date1').val(data.end_date);
        // Initialize Select2 for type (same as school implementation)
        $('#announcement-type').select2({
          dropdownParent: $('#offcanvasAddUser'),
          placeholder: '{{ __("Select type") }}',
          allowClear: true,
          width: '100%'
        }).val(data.type).trigger('change');
        // Change form action to update route
        $('#addNewUserForm').attr('action', '{{ route("announcements.update", ":id") }}'.replace(':id', id));
        // Add PUT method for update
        if ($('#addNewUserForm input[name="_method"]').length === 0) {
          $('#addNewUserForm').append('<input type="hidden" name="_method" value="PUT">');
        }
        // Change modal title
        $('#offcanvasAddUserLabel').text('{{ __("Edit Announcement") }}');
        $('#updateLavel').text('{{__('Edit')}}');
        // Show the modal
        $('#offcanvasAddUser').offcanvas('show');
        // on click cancel  or modal closed reload the page
        $('#offcanvasAddUser').on('hidden.bs.offcanvas', function () {
          location.reload();
        });
      },
      error: function (error) {
        console.log('Error:', error);
        toastr.error('{{ __("Failed to fetch announcement data.") }}', '{{ __("Error") }}');
      }
    });
  }

  function handleViewAnnouncement(id) {
    let url = '{{ route("announcements.show", ":id") }}'.replace(':id', id);
    console.log(url);
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        // $('#announcementTitle').text(data.title || '');
        // $('#announcementDescription').text(data.description || '');
        // $('#announcementStart').text(data.start_date || '');
        // $('#announcementEnd').text(data.end_date || '');
        // $('#announcementType').text(data.type || '');

        // const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasViewAnnouncement'));
        // offcanvas.show();
        $('#modal-title').text(data.title || '');
        $('#modal-description').text(data.description || '');
        $('#modal-valid-from').text(data.start_date || '');
        $('#modal-valid-to').text(data.end_date || '');
        $('#modal-type').text(data.type || '');
        $('#created').text(data.created_at || '');
        //Open the modal
        $("#modalCenter").modal('show');
      },
      error: function (error) {
        console.error('Error:', error);
        toastr.error('{{ __("Failed to fetch announcement data.") }}', '{{ __("Error") }}');
      }
    });
  }

  function deleteAnnoucement(url){
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
              console.log(error);
            }
          })
        }
      })
    }

</script>
@if (isset($is_already_visited) && $is_already_visited == false)
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const tour = new Shepherd.Tour({
        defaultStepOptions: {
          scrollTo: false,
          cancelIcon: { enabled: true }
        },
        useModalOverlay: true
      });
      const stepsInput = [
        {
          title: "{{__('restaurant_announcements.tour.table_title')}}",
          text: "{{__('restaurant_announcements.tour.table_text')}}",
          element: '.card-datatable',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_announcements.tour.search_title')}}",
          text: "{{__('restaurant_announcements.tour.search_text')}}",
          element: '.dataTables_filter input',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_announcements.tour.add_title')}}",
          text: "{{__('restaurant_announcements.tour.add_text')}}",
          element: '.create-post-btn',
          position: 'bottom',
          nextText: 'Continue',
          finishText: 'Finish'
        }
      ];
      const steps = typeof generateTourSteps === 'function' ? generateTourSteps(stepsInput) : stepsInput;
      steps.forEach(step => tour.addStep(step));
      tour.start();
    });
  </script>
@endif
@endsection

@section('content')

  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // show old data in the moda
        $('#announcement-id').val('{{ old('id') }}');
        $('#announcement-title').val('{{ old('title') }}');
        $('#announcement-description').val('{{ old('description') }}');
        $('#flatpickr-date').val('{{ old('start_date') }}');
        $('#flatpickr-date1').val('{{ old('end_date') }}');
        // Change modal title
        $('#offcanvasAddUserLabel').text('{{ __("Edit Announcement") }}');
        $('#updateLavel').text('{{__('Edit')}}');
        // Show the modal
        $('#offcanvasAddUser').offcanvas('show');
        $('#offcanvasAddUser').on('hidden.bs.offcanvas', function () {
            location.reload();
          });
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
          <th>{{__('title')}}</th>
          <th>{{__('description')}}</th>
          <th>{{__('valid from')}}</th>
          <th>{{__('valid to')}}</th>
          <th>{{__('action')}}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Add Announcement')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('announcements.store') }}" method="post">
        @csrf
        <input type="hidden" name="announcement_id" id="announcement-id" value="">
        <div class="mb-6">
          <label class="form-label" for="announcement-title">{{__('title')}}</label>
          <input type="text" class="form-control" id="announcement-title" placeholder="{{ __('Enter announcement title') }}" name="title" required/>
          <small class="text-danger">{{ $errors->first('title') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="announcement-description">{{__('description')}}</label>
          <textarea class="form-control" id="announcement-description" placeholder="{{ __('Enter announcement description') }}" name="description" rows="3" required></textarea>
          <small class="text-danger">{{ $errors->first('description') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="flatpickr-date">{{__('valid from')}}</label>
          <input type="text" class="form-control" name="start_date" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
          <small class="text-danger">{{ $errors->first('start_date') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="flatpickr-date1">{{__('valid to')}}</label>
          <input type="text" class="form-control" name="end_date" placeholder="YYYY-MM-DD" id="flatpickr-date1" required />
          <small class="text-danger">{{ $errors->first('end_date') }}</small>
        </div>
        <div class="mb-6">
          @php
            $announcement_type = config('constant.restaurant_announcements');
          @endphp
          <label class="form-label" for="announcement-type">{{ __('Type') }}</label>
          <select id="announcement-type" class="select2 form-select @error('type') is-invalid @enderror" name="type" required>
            <option value="">{{ __('Select type') }}</option>
            @foreach($announcement_type as $key => $value)
              <option value="{{$key}}">{{ __(ucfirst($value)) }}</option>
            @endforeach
          </select>
          @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">{{__('Add')}}</button>
        <button type="reset" id="cancelButton" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasViewAnnouncement" aria-labelledby="offcanvasViewAnnouncementLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasViewAnnouncementLabel" class="offcanvas-title">View Announcement</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-1">
    <div class="card shadow-none border-0 mb-0">
      <div class="card-body p-0">
        <ul class="list-group list-group-flush border-0">
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-bell me-2"></i>{{__('title')}}:</strong> <span id="announcementTitle"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-align-left me-2"></i>{{__('description')}}</strong>
            <div id="announcementDescription" style="white-space: pre-line;"></div>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-calendar me-2"></i>{{__('valid from')}}:</strong> <span id="announcementStart"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-calendar me-2"></i>{{__('valid to')}}:</strong> <span id="announcementEnd"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-tag me-2"></i>{{__('type')}}:</strong> <span id="announcementType"></span>
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
                      <h5 class="modal-title" id="modalCenterTitle">{{ __('Annoucement Details') }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                  </div>
                  <div class="modal-body">
                      <ul class="list-unstyled">
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-bell ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('title')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-title"></span>
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
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('valid from')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-valid-from"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('valid to')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-valid-to"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-tag ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('type')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-type"></span>
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
