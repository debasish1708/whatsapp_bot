@extends('layouts.layoutMaster')

@section('title', __('Form Applications'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
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
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
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
                    title: "{{__('applications.school.tour.applications_title')}}",
                    text: "{{__('applications.school.tour.applications_text')}}",
                    element: '.applications-list',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('applications.school.tour.search_title')}}",
                    text: "{{__('applications.school.tour.search_text')}}",
                    element: '.dataTables_filter input',
                    position: 'right',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('applications.school.tour.filter_title')}}",
                    text: "{{__('applications.school.tour.filter_text')}}",
                    element: '.nav-pills',
                    position: 'bottom',
                    nextText: 'Continue',
                    finishText: 'Finish'
                },
            ];
            const steps = generateTourSteps(stepsInput);
            console.log(steps);
            steps.forEach(step => tour.addStep(step));
            tour.start();
        });
    </script>
@endif
<script>
  // Inject enum values from PHP to JS
  window.SchoolAdmissionStatus = @json(collect(App\Enums\SchoolAdmissionStatus::cases())->mapWithKeys(fn($case) => [$case->name => $case->value]));

  document.addEventListener('DOMContentLoaded', function (e) {
    const statusMap = {
      [window.SchoolAdmissionStatus.NEW]: { title: '{{ __("New") }}', class: 'bg-label-warning' },
      [window.SchoolAdmissionStatus.INPROCESS]: { title: '{{ __("In Process") }}', class: 'bg-label-info' },
      [window.SchoolAdmissionStatus.ACCEPTED]: { title: '{{ __("Accepted") }}', class: 'bg-label-success' },
      [window.SchoolAdmissionStatus.REJECTED]: { title: '{{ __("Rejected") }}', class: 'bg-label-danger' }
    };
    var statusFilter = window.SchoolAdmissionStatus.ALL;

    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '{!! route('school.admission.index') !!}',
        data: function (d) {
          d.status = statusFilter;
        }
      },
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __("ID") }}' },
        { data: 'name', name: 'name', title: '{{ __("Name") }}' },
        {
          data: 'status',
          name: 'status',
          render: function(data, type, row) {
            const status = statusMap[data] || { title: data, class: 'bg-label-secondary' };
            return `<span class="badge ${status.class}">${status.title}</span>`;
          }
        },
        { data: 'payment', name: 'payment', title: '{{ __("Payment") }}' },
        { data: 'created_at', name: 'created_at', title: '{{ __("Submitted At") }}' },
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

    $('.nav-pills .nav-link').on('click', function (e) {
      e.preventDefault();
      $('.nav-pills .nav-link').removeClass('active');
      $(this).addClass('active');

      statusFilter = $(this).data('table');

      table.ajax.reload();
    });
    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', '{{ __("Search Application...") }}');
  });

  function handleStatusButtons(url, type){
    let message=type=='approve' ? '{{ __("Are you sure you want to approve the application?") }}' : '{{ __("Are you sure you want to reject the application?") }}';
    if(type=='refund'){
        message='{{ __("Are you sure you want to refund the payment for this application?") }}';
    }
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
            Swal.fire({
              icon: 'success',
              title:"{{ __("Success") }}",
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
          }
        })
      }
    })
  }

  function handleShowApplication(id){
      $.ajax({
          url:"{{route('school.admissions.show',':id')}}".replace(':id', id),
          method:'get',
          success:function(data){
              setModalData(data);
              $("#modalCenter").modal('show');
          },
          error:function(error){
          }
      });
  }
  function setModalData(data){
      $("#first-name").text(data.first_name);
      $("#last-name").text(data.last_name);
      $("#email").text(data.email);
      $("#mobile-number").text(data.mobile_number);
      $("#dob").text(data.dob);
      $("#gender").text(data.gender);
      $("#address").text(data.address);
      $("#city").text(data.city);
      $("#status").text(data.status);
      $("#payment-status").text(data.payment_status);
      $("#created").text(data.created_at);
  }
</script>
@endsection

@section('page-style')
<style>
  /* Normal (inactive) button style */
  .nav-pills .nav-link {
    color: #555;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease-in-out;
  }
  /* Active tab gets gradient */
  .nav-pills .nav-link.active {
    background: linear-gradient(135deg, #7F00FF 0%, #E100FF 100%);
    color: #fff !important;
    font-weight: bold;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    transform: scale(1.02);
  }
</style>
@endsection

@section('content')
<ul class="nav nav-pills mb-3">
  <li class="nav-item">
    <button class="nav-link active" data-table="all" type="button">
      {{ __('All') }}
    </button>
  </li>
  @foreach(App\Enums\SchoolAdmissionStatus::cases() as $case)
    <li class="nav-item">
      <button class="nav-link" data-table="{{ $case->value }}" type="button">
        {{ __(ucwords(str_replace('_', ' ', strtolower($case->name)))) }}
      </button>
    </li>
  @endforeach
</ul>
<!-- DataTables for each filter -->
<div class="card applications-list">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Payment') }}</th>
          <th>{{ __('Submitted At') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<div class="col-lg-4 col-md-6">
  <div class="mt-4">
    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCenterTitle">{{ __('Application Details') }}</h5>
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
                    <span class="fw-semibold me-1 m-2">{{ __('First Name') }}:</span>
                  </div>
                  <span class="text-break m-2" id="first-name"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-user ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Last Name') }}:</span>
                  </div>
                  <span class="text-break m-2" id="last-name"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-mail ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Email') }}:</span>
                  </div>
                  <span class="text-break m-2" id="email"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-phone ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Mobile Number') }}:</span>
                  </div>
                  <span class="text-break m-2" id="mobile-number"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-calendar-event ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Date of Birth') }}:</span>
                  </div>
                  <span class="text-break m-2" id="dob"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-gender-bigender ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Gender') }}:</span>
                  </div>
                  <span class="text-break m-2" id="gender"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-map-pin ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Address') }}:</span>
                  </div>
                  <span class="text-break m-2" id="address"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-map ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('City') }}:</span>
                  </div>
                  <span class="text-break m-2"id="city"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-progress ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Status') }}:</span>
                  </div>
                  <span class="text-break m-2" id="status"></span>
                </div>
              </li>
              <li class="mb-2">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-receipt-dollar ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Payment Status') }}:</span>
                  </div>
                  <span class="text-break m-2" id="payment-status"></span>
                </div>
              </li>
              <li class="mb-2 pt-1">
                <div class="d-flex">
                  <div class="d-flex h-100">
                    <div class="badge bg-label-primary p-1 m-1 rounded">
                      <i class='ti ti-calendar ti-sm'></i>
                    </div>
                    <span class="fw-semibold me-1 m-2">{{ __('Applied On') }}:</span>
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
