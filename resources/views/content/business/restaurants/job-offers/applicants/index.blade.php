@extends('layouts.layoutMaster')

@section('title', __('Applicants - School Management'))

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
<script>
  document.addEventListener('DOMContentLoaded', function () {

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('job-offers.applicants', ['jobOffer' => $jobOffer->id]) !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __("ID") }}' },
        { data: 'name', name: 'name', title: '{{ __("Name") }}' },
        { data: 'email', name: 'email', title: '{{ __("Email") }}' },
        { data: 'mobile_number', name: 'mobile_number', title: '{{ __("Mobile Number") }}' },
        { data: 'gender', name: 'gender', title: '{{ __("Gender") }}' },
        { data: 'resume', name: 'resume', title: '{{ __("Resume") }}' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __("Actions") }}' }
      ] ,
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"' +
              '<"me-2"f>' +
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
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip()
    });

    $('.dataTables_filter input').attr('placeholder', '{{ __('Search Applicants...') }}');
  });

  function viewApplicant(id) {
    $.ajax({
      url: "{{ route('restaurant.job-applicant.show', ':id') }}".replace(':id', id),
      type: 'GET',
      success: function(response) {
        $('#modal-applicant-name').text(response.name || '');
        $('#modal-applicant-email').text(response.email || '');
        $('#modal-applicant-mobile').text(response.mobile_number || '');
        $('#modal-applicant-gender').text(response.gender || '');
        $('#modal-applicant-dob').text(response.dob || '');
        $('#modal-applicant-address').text(response.address || '');
        $('#modal-applicant-city').text(response.city || '');
        if(response.resume) {
          $('#modal-applicant-resume').attr('href', response.resume).text('View Resume').show();
        } else {
          $('#modal-applicant-resume').hide();
        }
        $("#modalApplicant").modal('show');
      },
      error: function(xhr) {
        alert('Failed to load applicant details.');
      }
    });
  }

  function deleteApplicant(url){
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

@endsection

@section('content')

<div class="w-100 d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0">{{ __('Applicant Details') }}</h4>
    <a href="{{route('job-offers.index')}}" class="btn btn-primary btn-custom">
        {{ __('Back') }}
    </a>
</div>
<!-- Users List Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>{{ __('Job Offer ID') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Email') }}</th>
          <th>{{ __('Mobile Number') }}</th>
          <th>{{ __('Gender') }}</th>
          <th>{{ __('Resume') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Offcanvas for Viewing Applicant Details -->
<div class="modal fade" id="modalApplicant" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalApplicantTitle">{{ __('Applicant Details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-unstyled">
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-user ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Name')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-name"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-mail ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Email')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-email"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-phone ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Mobile Number')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-mobile"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-gender-bigender ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Gender')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-gender"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-calendar ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Date of Birth')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-dob"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-map-pin ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Address')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-address"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-building ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('City')}}:</span>
              </div>
              <span class="text-break m-2" id="modal-applicant-city"></span>
            </div>
          </li>
          <li class="mb-2">
            <div class="d-flex">
              <div class="d-flex h-100">
                <div class="badge bg-label-primary p-1 m-1 rounded">
                  <i class='ti ti-file-text ti-sm'></i>
                </div>
                <span class="fw-semibold me-1 m-2">{{__('Resume')}}:</span>
              </div>
              <a class="text-break m-2" id="modal-applicant-resume" href="#" target="_blank"></a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection
