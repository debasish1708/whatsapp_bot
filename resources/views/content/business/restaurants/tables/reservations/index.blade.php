@extends('layouts.layoutMaster')

@section('title', __('Table Reservations'))

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss', 'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
  @vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
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
  @vite(['resources/assets/js/forms-selects.js'])
  <script>

    document.addEventListener('DOMContentLoaded', function () {
      // Initialize the datatable
      var table = $('.datatables-users').DataTable({
        processing: true,
        serverSide: false,
        ajax: '{{ route('reservation.index') }}',
        columns: [
          { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
          { data: 'table_number', title: '{{ __('Table Number') }}' },
          { data: 'capacity', title: '{{ __('Capacity') }}' },
          { data: 'slot_timing', title: '{{ __('Slot Timing') }}' },
          { data: 'customer_info', title: '{{ __('Customer Info') }}' },
          {
            data: 'booking_info',
            title: '{{ __("Booking Info") }}',
            orderData: [7] // refer to booking_date_raw index
          },
          // { data: 'booking_info', title: '{{ __('Booking Info') }}' },
          { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __('Actions') }}' },
          { data: 'booking_date_raw', visible: false, searchable: false } // hidden but used for sorting
        ],
        dom: '<"row"' +
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
          // {
          //   text: '<i class="ti ti-eye me-1"></i> {{ __('View All Bookings') }}',
          //   className: 'btn btn-info create-post-btn bg-gradient-info-custom',
          //   action: function (e, dt, node, config) {
          //     // Refresh the table to show all bookings
          //     table.ajax.reload();
          //   }
          // }
        ],
        drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
        language: {
          "emptyTable": "{{ __('No reservations available') }}",
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
      $('.dataTables_filter input').attr('placeholder', '{{__('Search reservations...')}}');
    });

    function handleStatusButtons(url, type) {
      let message = type == 'accept' ? '{{ __("Are you sure you want to approve the Reservation?") }}' : '{{ __("Are you sure you want to reject the Reservation?") }}';
      Swal.fire({
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            method: 'PUT',
            url: url,
            data: {
              _token: "{{ csrf_token() }}"
            },
            success: function (result) {
              console.log(result);
              Swal.fire({
                icon: 'success',
                title: "{{ __("Success") }}",
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
              console.log(error);
            }
          })
        }
      })
    }

    function handleDeleteReservation(url) {
      Swal.fire({
        text: "{{ __('Are you sure you want to delete this reservation ?') }}",
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
                title: error.responseJSON?.message || "{{ __('Error occurred') }}",
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
  @if (isset($is_visited) && $is_visited == false)
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const tour = new Shepherd.Tour({
          defaultStepOptions: {
            scrollTo: false,
            cancelIcon: { enabled: true }
          },
          useModalOverlay: true
        });
        const stepsInput = [
          {
            title: "{{__('restaurant_table_reservations.tour.table_title')}}",
            text: "{{__('restaurant_table_reservations.tour.table_text')}}",
            element: '.card-datatable',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_table_reservations.tour.search_title')}}",
            text: "{{__('restaurant_table_reservations.tour.search_text')}}",
            element: '.dataTables_filter input',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_table_reservations.tour.actions_title')}}",
            text: "{{__('restaurant_table_reservations.tour.actions_text')}}",
            element: '.datatables-users',
            position: 'top',
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
  <h4>{{__('Table Reservations')}}</h4>
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead class="border-top">
          <tr>
            <th>ID</th>
            <th>{{__('Table Number')}}</th>
            <th>{{__('Capacity')}}</th>
            <th>{{__('Slot Timing')}}</th>
            <th>{{__('Customer Info')}}</th>
            <th>{{__('Booking Info')}}</th>
            <th>{{__('Actions')}}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection