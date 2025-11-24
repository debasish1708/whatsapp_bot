@extends('layouts.layoutMaster')

@section('title', 'Orders')

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
@if (isset($is_visited) && $is_visited == false)
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
          title: 'Orders',
          text: 'This is orders page. Here all restaurant orders are listed. You can view order details, mark as deliver the order and cancel the order.',
          element: '.card-datatable',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: 'Search orders',
          text: 'Use this box to search for orders by user, amount, date etc.',
          element: '.dataTables_filter input',
          position: 'bottom',
          nextText: 'Continue',
          finishText: 'Finish'
        },
        // {
        //   title: 'Add Offer',
        //  text: 'By clicking this button, you can be able to add new offers. You can specify the title, description, discount precentage/price and applicable items.',
        //  element: '.create-post-btn',
        //  position: 'bottom',
        //  nextText: 'Continue',
        //  finishText: 'Finish'
        // }
      ];
      const steps = typeof generateTourSteps === 'function' ? generateTourSteps(stepsInput) : stepsInput;
      steps.forEach(step => tour.addStep(step));
      tour.start();
    });
  </script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var flatpickrDate = document.querySelector('#flatpickr-date');
        var flatpickrDate1 = document.querySelector('#flatpickr-date1');
        var date = document.querySelector('.date');
        var date1 = document.querySelector('.date1');
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
        if (date) {
            date.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today',
                onChange: function(selectedDates, dateStr) {
                    // Update the minimum date of the end date picker
                    if (date1._flatpickr) {
                        date1._flatpickr.set('minDate', dateStr);
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
        if (date1) {
            date1.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today' // Initial minimum date
            });
        }
        const statusMap = {
            pending:   { title: '{{__('Pending')}}', class: 'bg-label-warning' },
            delivered: { title: '{{__('Delivered')}}', class: 'bg-label-success' },
            canceled:  { title: '{{__('Canceled')}}', class: 'bg-label-danger' }
        };

        const paymentStatusMap = {
            paid:   { title: '{{__('Paid')}}', class: 'bg-label-success' },
            unpaid: { title: 'Unpaid', class: 'bg-label-secondary' }
        };
        // Initialize the datatable
        var table = $('.datatables-users').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('restaurant.orders.index') }}',
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
                { data: 'user.name' },
                { data: 'user.mobile_number' },
                { data: 'total_amount' },
                { data: 'status',
                    render: function (data, type, row) {
                        const status = statusMap[data] || { title: data, class: 'bg-label-secondary' };
                        return `<span class="badge ${status.class}">${status.title}</span>`;
                    }
                },
                { data: 'payment_status',
                    render: function (data, type, row) {
                        const status = paymentStatusMap[data] || { title: data, class: 'bg-label-secondary' };
                        return `<span class="badge ${status.class}">${status.title}</span>`;
                    }
                },
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
        $('.dataTables_filter input').attr('placeholder', "{{__('Search orders...')}}");
    });

    function clearAddFormFields(){
        $('.add-error').text('');
        $('.add-field').val('');
    }

    function handleStatusButtons(url, type){
        console.log(url);
      // let url=$(this).data('url');
      let message=type=='delivered' ? "{{__('Are you sure you want to mark order as delivered?')}}" : "{{__('Are you sure you want to cancel the order?')}}";

      let swalOptions = {
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      };

      //let url=$(this).data('url');
      Swal.fire(swalOptions).then((result) => {
        if (result.isConfirmed) {

          let ajaxData = {
            _token: "{{ csrf_token() }}"
          };
          // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
          $.ajax({
            method: 'PUT',
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
<h4>{{__('Orders')}}</h4>
<div class="card">
    <div class="card-datatable table-responsive">
        <table class="datatables-users table">
            <thead class="border-top">
                <tr>
                    <th>ID</th>
                    <th>{{__('name')}}</th>
                    <th>{{__('mobile number')}}</th>
                    <th>{{__('total amount')}}</th>
                    <th>{{__('status')}}</th>
                    <th>{{__('payment status')}}</th>
                    <th>{{__('created at')}}</th>
                    <th>{{__(key: 'actions')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Offcanvas to add new user -->

@endsection