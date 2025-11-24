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
      ajax: '{{ route('schools.index') }}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'logo', orderable:false, searchable:false },
        { data: 'user.name' },
        { data: 'address' },
        {
            data: 'is_profile_completed',
            name:"profile_status" ,
            orderable:false, searchable:false,
            render: function (data, type, row) {
                let statusMap = {
                'active': { title: 'Active', class: 'bg-label-success' },
                'inactive': { title: 'Inactive', class: 'bg-label-warning' },
                'expired': { title: 'Expired', class: 'bg-label-danger' }
                };
                let key = (data || '').toString().toLowerCase();
                let statusObj = statusMap[key];
                if (data) {
                    return `<span class="badge bg-label-success">Completed</span>`;
                } else {
                    return `<span class="badge bg-label-warning">Incomplete</span>`;
                }
          }
        },
        {
            data: 'user.status'
        },
        { data: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
      ],
      columnDefs:[
        {
            width:"250px",
            targets:[3]
        },
        {
            className: "text-center",
            targets: [1,4]
        }
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
    $('.dataTables_filter input').attr('placeholder', 'Search schools...');
    });

    function handleStatusButtons(url, type){
        console.log(url);
      // let url=$(this).data('url');
      let message=type=='approve' ? 'Are you sure you want to approve the school?' : 'Are you sure you want to reject the school?';
      if(type=='delete'){
        message='Are you sure you want to delete the school?';
      }

      let swalOptions = {
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      };

      if(type == 'reject'){
        swalOptions.title = 'Reason';
        swalOptions.input = 'text';
        swalOptions.inputAttributes = { autocapitalize: 'off' };
        swalOptions.inputValidator = (value) => {
          if (!value) {
            return 'You need to provide a reason!';
          }
        };
      }

      //let url=$(this).data('url');
      Swal.fire(swalOptions).then((result) => {
        if (result.isConfirmed) {

          let ajaxData = {
            _token: "{{ csrf_token() }}"
          };
          if (type == 'reject') {
            ajaxData.reason = result.value;
          }

          // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
          $.ajax({
            method: type=='delete' ? 'DELETE' : 'PUT',
            url: url,
            data: ajaxData,
            success: function (result) {
              toastr.success(result.message, 'Success');
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
  <h4>Schools</h4>
  <div class="card">
    <div class="card-datatable table-responsive">
        <table class="datatables-users table">
            <thead class="border-top">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Logo</th>
                    <th>name</th>
                    <th>address</th>
                    <th class="text-center">Profile status</th>
                    <th class="text-center">status</th>
                    <th>Created at</th>
                    {{-- <th>Status</th>
                    <th>Type</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
  </div>
@endsection