@extends('layouts.layoutMaster')

@section('title', __('Students - School Management'))

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
@vite([
  'resources/assets/js/modal-add-new-cc.js',
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
                    title: "{{__('students.school.tour.students_title')}}",
                    text: "{{__('students.school.tour.students_text')}}",
                    element: '.card-datatable',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('students.school.tour.add_title')}}",
                    text: "{{__('students.school.tour.add_text')}}",
                    element: '.create-post-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('students.school.tour.import_title')}}",
                    text: "{{__('students.school.tour.import_text')}}",
                    element: '.import-post',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('students.school.tour.search_title')}}",
                    text: "{{__('students.school.tour.search_text')}}",
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
      ajax: '{!! route('school.students.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __("ID") }}' },
        { data: 'user.name', title: '{{ __("Name") }}' },
        { data: 'user.mobile_number', title: '{{ __("Mobile Number") }}' },
        { data: 'added_by', name: 'added_by', title: '{{ __("Added By") }}' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __("Action") }}' },
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
          text: '<i class="ti ti-plus me-1"></i> {{ __("Add Student") }}',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Reset form
            $('#addJobOfferForm')[0].reset();

            $('#addJobOfferForm')
              .attr('action', '{{ route("school.students.store") }}')
              .find('input[name="_method"]').remove(); // Remove any hidden _method=PUT input

            // Reset modal labels
            $('#addJobOfferModalLabel').text('{{ __("Add Student") }}');
            $('#submitButton').text('{{ __("Submit") }}');

            // Clear validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Show offcanvas
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
            offcanvas.show();
          }
        },
        {
          text: '<i class="ti ti-upload me-1"></i> {{ __("Import") }}',
          className: 'btn btn-primary ms-2 import-post bg-gradient-primary-custom',
          action: function (e, dt, node, config) {
            // Show offcanvas/modal
            const modal = new bootstrap.Modal(document.getElementById('addNewCCModal'));
            modal.show();
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
        $('.dataTables_filter input').attr('placeholder', '{{ __("Search Students...") }}');
  });

    // Listen for edit button click
  function handleEditJobOffer(id) {
    let url = "{{route('school.students.edit',':id')}}".replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        console.log(data);
        // Fill form fields
        $('#student-id').val(data.id);
        $('#name').val(data.name);
        $('#mobile-number').val(data.mobile_number);

        // Update form action to PUT
        $('#addJobOfferForm').attr('action', "{{route('school.students.update',':id')}}".replace(':id', id));
        if ($('#addJobOfferForm input[name="_method"]').length === 0) {
          $('#addJobOfferForm').append('<input type="hidden" name="_method" value="PUT">');
        }

        // Update modal title and button
        $('#addJobOfferModalLabel').text('{{ __("Edit Student") }}');
        $('#submitButton').text('{{ __("Update") }}');
        $('#submitButton').attr('data-loading-text', '{{ __("Updating...") }}');

        // Show modal
        let jobOffcanvas = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
        jobOffcanvas.show();
      },
      error: function (xhr) {
        console.error('Failed to load job offer:', xhr);
        toastr.error('Could not fetch Student Data.', 'Error');
      }
    });
  }

    function deleteItem(url){
      // let url=$(this).data('url');
      Swal.fire({
        text: "{{ __('Are you sure?') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light btn-custom',
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
                title:"Success",
                text: result.message,
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light btn-custom'
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
                  confirmButton: 'btn btn-primary waves-effect waves-light btn-custom'
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
      // Open the modal
      const addJobOfferModal = new bootstrap.Offcanvas(document.getElementById('addJobOfferModal'));
      addJobOfferModal.show();

      // Set form action and button text based on whether it's an update or create
      const form = $('#addJobOfferForm');
      const modalLabel = $('#addJobOfferModalLabel');
      const submitButton = $('#submitButton');
      const studentId = '{{ old('student_id') }}';

      if (studentId) {
        let updateUrl = "{{ route('school.students.update', ':id') }}".replace(':id', studentId);
        form.attr('action', updateUrl);
        if (form.find('input[name="_method"]').length === 0) {
          form.prepend('<input type="hidden" name="_method" value="PUT">');
        }
        modalLabel.text('{{ __("Edit Student") }}');
        submitButton.text('{{ __("Update") }}');
      } else {
        modalLabel.text('{{ __("Add Student") }}');
        submitButton.text('{{ __("Submit") }}');
      }
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
          <th>{{ __('Mobile Number') }}</th>
          <th>{{ __('Added By') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addJobOfferModal" aria-labelledby="addJobOfferModalLabel">
    <div class="offcanvas-header">
      <h5 id="addJobOfferModalLabel" class="offcanvas-title">{{ __('Add Student') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-user pt-0" id="addJobOfferForm" action="{{ route('school.students.store') }}" method="POST">
        @csrf
        <input type="hidden" name="student_id" id="student-id">
        <div class="mb-3">
          <label class="form-label" for="name">{{ __('Name') }}</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="mobile-number">{{ __('Mobile Number') }}</label>
          <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile-number" name="mobile_number" value="{{ old('mobile_number') }}"
          maxlength="15" oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')" placeholder="" required>
          @error('mobile_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" id="submitButton" class="btn btn-primary me-3 data-submit btn-custom"
          data-loading-text="{{ __('Submitting...') }}"
          onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }">
          {{ __('Submit') }}
        </button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>
</div>

@include('_partials/_modals/modal-add-new-cc')
@endsection
