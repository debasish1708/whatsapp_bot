@extends('layouts.layoutMaster')

@section('title', __('Tables'))

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
    function clearTableHourFormFields() {

    }

    document.addEventListener('DOMContentLoaded', function () {
      // Initialize the datatable
      var table = $('.datatables-users').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('tables.index') }}',
        columns: [
          { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
          { data: 'number', title: '{{ __('Table Number') }}' },
          { data: 'capacity', title: '{{ __('Capacity') }}' },
          { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __('Actions') }}' },
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
          {
            text: '<i class="ti ti-plus me-1"></i> {{ __("Add Table") }}',
            className: 'btn btn-primary create-post-btn bg-gradient-primary-custom me-2', // <-- Add margin-end here
            action: function (e, dt, node, config) {
              clearAddFormFields();
              $('#offcanvasAddUser').offcanvas('show');
            }
          },
          {
            text: '<i class="ti ti-plus me-1"></i> {{ __("Add Hours") }}',
            className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
            action: function (e, dt, node, config) {
              clearTableHourFormFields();
              $('#offcanvasAddTableHours').offcanvas('show');
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
      $('.dataTables_filter input').attr('placeholder', '{{__('Search tables...')}}');
    });

    function clearAddFormFields() {
      $('.add-error').text('');
      $('.add-field').val('');
    }
    function clearEditFormFields() {
      $('.edit-error').text('');
      $('.edit-input').val('');
    }
    function setEditDataToForm(data) {
      $("#table-id").val(data.id);
      $("#table-number-edit").val(data.number);
      $("#table-capacity-edit").val(data.capacity);
      const form = document.getElementById('edit-table-form');
      const actionTemplate = form.getAttribute('data-action-template');
      const updatedAction = actionTemplate.replace(':id', data.id);
      form.setAttribute('action', updatedAction);
      $('#offcanvasEditUser').offcanvas('show');
    }
    function handleEditTable(data) {
      clearEditFormFields();
      setEditDataToForm(data);
    }
    function handleDeleteTable(url) {
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
            title: "{{__('restaurant_tables.tour.table_title')}}",
            text: "{{__('restaurant_tables.tour.table_text')}}",
            element: '.card-datatable',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_tables.tour.search_title')}}",
            text: "{{__('restaurant_tables.tour.search_text')}}",
            element: '.dataTables_filter input',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_tables.tour.add_title')}}",
            text: "{{__('restaurant_tables.tour.add_text')}}",
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
    @php
      // Prefer the posted edit id if validation failed on the edit form
      $editId = old('table_id') ?? session('table_id') ?? session('table');
    @endphp
    @if ($editId)
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          let id = "{{$editId}}";
          let url = "{{route('tables.edit', ':id')}}".replace(':id', id);
          $.ajax({
            url: url,
            method: 'get',
            success: function (data) {
              clearEditFormFields();
              setEditDataToForm(data.data);
            },
            error: function (error) {
              console.log(error);
            }
          });
        })
      </script>
    @else
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          $('#offcanvasAddUser').offcanvas('show');
        })
      </script>
    @endif
  @endif
  <h4>{{__('Tables')}}</h4>
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead class="border-top">
          <tr>
            <th>ID</th>
            <th>{{__('Table Number')}}</th>
            <th>{{__('Capacity')}}</th>
            <th>{{__('Actions')}}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- Offcanvas to add new table -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Add Table')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('tables.store') }}" method="post">
        @csrf
        <div class="mb-6">
          <label class="form-label" for="table-number">{{__('Table Number')}}</label>
          <input type="text" class="form-control add-field" id="table-number" name="number" required
            value="{{old('number')}}" />
          <small class="text-danger add-error">{{ $errors->first('number') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="table-capacity">{{__('Capacity')}}</label>
          <input type="number" class="form-control add-field" id="table-capacity" name="capacity" required
            value="{{old('capacity')}}" min="1" />
          <small class="text-danger add-error">{{ $errors->first('capacity') }}</small>
        </div>
        <button type="submit" class="btn btn-primary me-3 data-submit btn-custom">{{__('Add Table')}}</button>
      </form>
    </div>
  </div>
  <!-- Offcanvas to edit table -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasEditUserLabel" class="offcanvas-title">{{__('Edit Table')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="edit-table-form" data-action-template="{{route('tables.update', ':id')}}"
        method="post">
        @csrf
        @method('put')
        <input type="hidden" name="table_id" id="table-id" value="">
        <div class="mb-6">
          <label class="form-label" for="table-number-edit">{{__('Table Number')}}</label>
          <input type="text" class="form-control edit-input" id="table-number-edit" name="number" required />
          <small class="text-danger edit-error">{{ $errors->first('number') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="table-capacity-edit">{{__('Capacity')}}</label>
          <input type="number" class="form-control edit-input" id="table-capacity-edit" name="capacity" required
            min="1" />
          <small class="text-danger edit-error">{{ $errors->first('capacity') }}</small>
        </div>
        <button type="submit" class="btn btn-primary me-3 data-submit btn-custom">{{__('Edit Table')}}</button>
      </form>
    </div>
  </div>



  @php
    // Get selected time slots from old input or existing table hours
    $selectedTimeSlots = old('time_slot', $tableHours->pluck('time_slot')->toArray() ?? []);

    // Define all available time slots
    $allTimeSlots = [
      '00:00 AM - 01:00 AM',
      '01:00 AM - 02:00 AM',
      '02:00 AM - 03:00 AM',
      '03:00 AM - 04:00 AM',
      '04:00 AM - 05:00 AM',
      '05:00 AM - 06:00 AM',
      '06:00 AM - 07:00 AM',
      '07:00 AM - 08:00 AM',
      '08:00 AM - 09:00 AM',
      '09:00 AM - 10:00 AM',
      '10:00 AM - 11:00 AM',
      '11:00 AM - 12:00 PM',
      '12:00 PM - 01:00 PM',
      '01:00 PM - 02:00 PM',
      '02:00 PM - 03:00 PM',
      '03:00 PM - 04:00 PM',
      '04:00 PM - 05:00 PM',
      '05:00 PM - 06:00 PM',
      '06:00 PM - 07:00 PM',
      '07:00 PM - 08:00 PM',
      '08:00 PM - 09:00 PM',
      '09:00 PM - 10:00 PM',
      '10:00 PM - 11:00 PM',
      '11:00 PM - 12:00 AM'
    ];
  @endphp
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddTableHours"
    aria-labelledby="offcanvasAddTableHoursLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddTableHours" class="offcanvas-title">{{__('Add Hours')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('restaurant.tables.hours.store') }}"
        method="post">
        @csrf
        <div class="mb-6">
          <label class="form-label" for="table_id">{{ __('Select hours') }}</label>
          <select id="time_slot" name="time_slot[]" class="select2 form-select @error('time_slot') is-invalid @enderror"
            required multiple>
            <option value="">-- Select Table --</option>

            @foreach($allTimeSlots as $timeSlot)
              <option value="{{ $timeSlot }}" {{ in_array($timeSlot, $selectedTimeSlots) ? 'selected' : '' }}>
                {{ $timeSlot }}
              </option>
            @endforeach

          </select>
          @error('time_slot')
            <p class="text-danger">{{ $message }}</p>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary me-3 data-submit btn-custom">{{__('Add Hours')}}</button>
      </form>
    </div>
  </div>

@endsection