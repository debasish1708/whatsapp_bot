@extends('layouts.layoutMaster')

@section('title', 'Schools')

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
  @vite(['resources/assets/js/forms-selects.js', 'resources/assets/js/forms-tagify.js', 'resources/assets/js/forms-typeahead.js'])

  <script>
    document.addEventListener('DOMContentLoaded', function () {

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('admin.users.index') }}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID', className: 'text-center' },
        { data: 'name' },
        { data: 'mobile_number' },
        { data: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
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
      buttons: [],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip()
    });

    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', 'Search Users...');
    });

    function deleteUser(url){
      Swal.fire({
        text: 'Are you Sure to Delete User',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
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
              toastr.sucecss(result.message, 'Success');
              $('.datatables-users').DataTable().ajax.reload(null, false);
            },
            error: function (error) {
              toastr.error(error.responseJSON.message, 'Error');
              console.log(error);
            }
          })
        }
      })
    }
  </script>
@endsection

@section('content')
  <!-- Users List Table -->
  <h4>Users</h4>
  <div class="card">
    <div class="card-datatable table-responsive">
        <table class="datatables-users table">
            <thead class="border-top">
                <tr>
                    <th class="text-center">ID</th>
                    <th>name</th>
                    <th>mobile number</th>
                    <th>Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
  </div>
@endsection