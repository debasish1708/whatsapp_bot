@extends('layouts.layoutMaster')

@section('title', __('Sos Alert - School Management'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
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

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/cards-advance.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
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

@section('page-script')
@vite([
  'resources/assets/js/forms-selects.js',
  'resources/assets/js/forms-tagify.js',
  'resources/assets/js/forms-typeahead.js'
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
                    title: "{{__('sos_alerts.school.tour.sos_alerts_title')}}",
                    text: "{{__('sos_alerts.school.tour.sos_alerts_text')}}",
                    element: '.alerts-card',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('sos_alerts.school.tour.add_title')}}",
                    text: "{{__('sos_alerts.school.tour.add_text')}}",
                    element: '.add-alert-card',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('sos_alerts.school.tour.search_title')}}",
                    text: "{{__('sos_alerts.school.tour.search_text')}}",
                    element: '.dataTables_filter input',
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
  document.addEventListener('DOMContentLoaded', function () {
    // Status mapping
    const statusMap = {
      1: { title: '{{ __('Pending') }}', class: 'bg-label-warning' },
      2: { title: '{{ __('Active') }}', class: 'bg-label-success' },
      3: { title: '{{ __('Inactive') }}', class: 'bg-label-secondary' }
    };

    // Initialize the datatable
    var table = $('.datatables-projects').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('school.sos-alerts.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: '{{ __('ID') }}' },
        { data: 'title', name: 'title', title: '{{ __('Title') }}' },
        { data: 'message', name: 'message', title: '{{ __('Message') }}' },
        {
          data: 'type',
          name: 'type',
          orderable: false,
          searchable: false,
          title: '{{ __('Type') }}',
          render: function (data, type, row) {
            const typeMap = {
              'emergency': { title: '{{ __('Emergency') }}', class: 'bg-label-danger' },
              'alert': { title: '{{ __('Alert') }}', class: 'bg-label-warning' },
              'fire_dril': { title: '{{ __('Fire Drill') }}', class: 'bg-label-danger' },
              'intrusion': { title: '{{ __('Intrusion') }}', class: 'bg-label-dark' },
              'medical_emergency': { title: '{{ __('Medical Emergency') }}', class: 'bg-label-primary' },
              'weather_alert': { title: '{{ __('Weather Alert') }}', class: 'bg-label-info' },
              'other': { title: '{{ __('Other') }}', class: 'bg-secondary text-white' }
            };
            const key = (data || '').toString().trim().toLowerCase().replace(/\s+/g, '_');
            const typeObj = typeMap[key];
            if (typeObj) {
              return `<span class="badge ${typeObj.class}">${typeObj.title}</span>`;
            } else {
              // fallback
              return `<span class="badge bg-label-secondary">${data}</span>`;
            }
          }
        },
        { data: 'created_at', name: 'created_at', title: '{{ __('Created At') }}' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, title: '{{ __('Actions') }}' },
      ],
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

    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', '{{ __('Search alert...') }}');

    // Set select2 placeholder for emergency type
    $('#emergency-type').select2({
      language: 'cs',
      dropdownParent: $('#emergency-form'),
      placeholder: '{{ __("Select type") }}',
      allowClear: true
    });
  });

  // Listen for edit button click
  function handleSosEdit(id) {
    let url = '{{ route("school.sos-alerts.edit", ":id") }}'.replace(':id', id);
    $.ajax({
      url: '/school/sos-alerts/' + id + '/edit',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        $('#sos_alert-id').val(data.id);
        $('#sos_alert-title').val(data.title);
        $('#sos_alert-message').val(data.message);
        $('#sos_alert-type').val(data.type).trigger('change'); // ✅ This is the correct way

        // Change form action to update route
        $('#addNewUserForm').attr('action', '{{ route("school.sos-alerts.update", ":id") }}'.replace(':id', id));
        // Add PUT method for update
        if ($('#addNewUserForm input[name="_method"]').length === 0) {
          $('#addNewUserForm').append('<input type="hidden" name="_method" value="PUT">');
        }
        // Change modal title
        $('#offcanvasAddUserLabel').text('{{ __('Edit Sos Alert') }}');
        $('#updateLavel').text('{{ __('Update') }}');
        $('#updateLavel').attr('data-loading-text', '{{ __('Updating...') }}');
        // Show the modal
        $('#offcanvasAddUser').offcanvas('show');
      },
      error: function () {
        toastr.error('Failed to fetch sos_alert data.', 'Error');
      }
    });
  }

  function showSosAleart(id) {
    $.ajax({
      url: '/school/sos-alerts/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (data) {

        const typeMap = {
          'emergency': { title: '{{ __('Emergency') }}', class: 'bg-label-danger' },
          'alert': { title: '{{ __('Alert') }}', class: 'bg-label-warning' },
          'fire_dril': { title: '{{ __('Fire Drill') }}', class: 'bg-label-danger' },
          'intrusion': { title: '{{ __('Intrusion') }}', class: 'bg-label-dark' },
          'medical_emergency': { title: '{{ __('Medical Emergency') }}', class: 'bg-label-primary' },
          'weather_alert': { title: '{{ __('Weather Alert') }}', class: 'bg-label-info' },
          'other': { title: '{{ __('Other') }}', class: 'bg-secondary text-white' }
        };

        // Normalize key
        const key = (data.type || '').toString().trim().toLowerCase().replace(/\s+/g, '_');

        // Get badge info
        const typeObj = typeMap[key];

        // Render formatted badge
        // if (typeObj) {
          // $('#sosType').html(`<span class="badge ${typeObj.class}">${typeObj.title}</span>`);
        // } else {
          // $('#sosType').html(`<span class="badge bg-label-secondary">${data.type}</span>`);
        // }

        if (typeObj) {
          $('#modal-type').html(`<span class="badge ${typeObj.class}">${typeObj.title}</span>`);
        } else {
          $('#modal-type').html(`<span class="badge bg-label-secondary">${data.type}</span>`);
        }


        // $('#sosTitle').text(data.title);
        // $('#sosMessage').text(data.message);
        // const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasViewSosAlert'));
        // offcanvas.show();

        $('#modal-title').text(data.title);
        $('#modal-message').text(data.message);
        $('#created').text(data.created_at);

        $("#modalCenter").modal('show');
      },
      error: function (error) {
        toastr.error('Failed to fetch sos alert data.', 'Error');
      }
    });
  }

  function deleteSosAlert(url){
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
              // Fix: Reload the correct DataTable
              $('.datatables-projects').DataTable().ajax.reload(null, false);
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
@if ($errors->editSosAlert->any() && session('sos_alert_edit'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddUser'));
      offcanvas.show();
    });
  </script>
@endif
@endsection

@section('content')
<!-- Users List Table -->
<div class="row g-4 py-4 alerts-card">
    <!-- Emergency Broadcast Trigger Form -->
    <div class="col-lg-4 col-md-5">
        <div class="card shadow-sm border-0 add-alert-card">
            <div class="card-header bg-opacity-25 py-3">
                <h5 class="card-title mb-0">
                    <span class="me-2">🚨</span>{{ __('Trigger Emergency Broadcast') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form id="emergency-form" action="{{ route('school.sos-alerts.store') }}" method="POST">
                  @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="emergency-title">{{ __('Title') }}</label>
                        <input type="text" class="form-control border-2" id="emergency-title" name="title" placeholder="{{ __('Enter emergency title') }}" required>
                        <small class="text-danger">{{ $errors->first('title') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="emergency-message">{{ __('Message') }}</label>
                        <textarea class="form-control border-2" id="emergency-message" name="message" rows="4" placeholder="{{ __('Enter detailed emergency message') }}" required></textarea>
                        <small class="text-danger">{{ $errors->first('message') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="emergency-type">{{ __('Type') }}</label>
                        <select class="select2 form-select form-select" data-style="btn-default" id="emergency-type" name="type">
                            @php
                              $sos_types=config('constant.school_sos_alerts');
                            @endphp
                            <option value=""></option>
                            @foreach ($sos_types as $key=>$value)
                              @php
                                $arr = explode(' ',$value,2);
                                $icon = $arr[0];
                                $type = $arr[1];
                              @endphp
                              <option value="{{ $key }}">{{ $icon.' '.__($type) }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger">{{ $errors->first('type') }}</small>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg py-3"
                          data-loading-text="{{ __('Sending...') }}"
                          onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class='spinner-border spinner-border-sm'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }">
                          <span class="me-2">🚨</span>{{ __('Send Alert') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Projects table -->
    <div class="col-xxl-8">
        <div class="card">
          <div class="card-datatable table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="datatables-projects table table-sm">
            <thead>
              <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Message') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Created At') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            </table>
          </div>
        </div>
    </div>
  </div>
    <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <!-- Header -->
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{ __('Edit Sos Alert') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="addNewUserForm" action="" method="post">
        @csrf
        <input type="hidden" name="sos_alert_id" id="sos_alert-id" value="">
        <div class="mb-6">
          <label class="form-label" for="sos_alert-title">{{ __('Title') }}</label>
          <input type="text" class="form-control" id="sos_alert-title" placeholder="{{ __('Enter psychological support name') }}" name="title" required/>
          <small class="text-danger">{{ $errors->editSosAlert->first('title') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="sos_alert-contact-phone">{{ __('Message') }}</label>
          <input type="text" class="form-control" id="sos_alert-message" name="message" placeholder="{{ __('Enter Mesage') }}" required />
          <small class="text-danger">{{ $errors->editSosAlert->first('message') }}</small>
        </div>
        {{-- make a type drop down --}}
        <div class="mb-6">
          <label class="form-label" for="sos_alert-type">{{ __('Type') }}</label>
          @php
            $sos_types=config('constant.school_sos_alerts');
          @endphp
          <select class="select2 form-select form-select" data-style="btn-default" id="sos_alert-type" name="type" required>
              <option value=""></option>
              @foreach ($sos_types as $key=>$value)
                @php
                  $arr = explode(' ',$value,2);
                  $icon = $arr[0];
                  $type = $arr[1];
                @endphp
                <option value="{{ $key }}">{{ $icon.' '.__($type) }}</option>
              @endforeach
          </select>
          <small class="text-danger">{{ $errors->editSosAlert->first('type') }}</small>
        </div>
        {{-- <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">Submit</button> --}}
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom"
          data-loading-text="{{ __('Updating...') }}"
          onclick="event.preventDefault(); if (this.form.checkValidity()) { this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.requestSubmit(); } else { this.form.reportValidity(); }">
          {{ __('Update') }}
        </button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasViewSosAlert" aria-labelledby="offcanvasViewSosAlertLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasViewSosAlertLabel" class="offcanvas-title">{{ __('View SOS Alert') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-0">
    <div class="card shadow-none border-0 mb-3 py-2">
      <div class="card-body p-0">
        <ul class="list-group list-group-flush border-0">
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-bell me-2"></i>{{ __('Title') }}:</strong> <span id="sosTitle"></span>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-message me-2"></i>{{ __('Message') }}:</strong>
            <div id="sosMessage" style="white-space: pre-line;"></div>
          </li>
          <li class="list-group-item border-0 py-1">
            <strong><i class="ti ti-tag me-2"></i>{{ __('Type') }}:</strong> <span id="sosType"></span>
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
                      <h5 class="modal-title" id="modalCenterTitle">{{ __('SOS Details') }}</h5>
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
                                      <span class="fw-semibold me-1 m-2">{{__('Title')}}:</span>
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
                                      <span class="fw-semibold me-1 m-2">{{ __('Message') }}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-message"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{ __('Type') }}:</span>
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
