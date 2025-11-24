@extends('layouts.layoutMaster')

@section('title', __('Members - Restaurant Management'))

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
        ajax: '{!! route('members.index') !!}',
        columns: [
          { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
          { data: 'name', name: 'name' },
          { data: 'mobile_number', name: 'mobile_number' },
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
          {
            text: '<i class="ti ti-plus me-1"></i> {{__("Add Member")}}',
            className: 'btn btn-primary create-post-btn btn-custom',
            action: function (e, dt, node, config) {
              // Reset form
              $('#addMemberForm')[0].reset();
              $('#member-id').val('');
              $('#addMemberForm').attr('action', '{{ route('members.store') }}');
              $('#addMemberForm input[name="_method"]').remove();
              $('#offcanvasAddMemberLabel').text('{{ __("Add Member") }}');
              $('#submitMemberBtn').text('{{ __("Add") }}');
              $('.is-invalid').removeClass('is-invalid');
              $('.invalid-feedback').remove();
              // Show offcanvas
              let memberOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddMember'));
              memberOffcanvas.show();
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
      $('.dataTables_filter input').attr('placeholder', '{{ __("Search members...") }}');
    });

    // Edit Member
    function handleEditMember(id) {
      let url = "{{route('members.edit', ':id')}}".replace(':id', id);
      $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          // Fill form fields
          $('#member-id').val(data.id);
          $('#user-id').val(data.user_id);
          $('#name').val(data.name);
          $('#mobile_number').val(data.mobile_number);

          // Update form action to PUT
          $('#addMemberForm').attr('action', "{{route('members.update', ':id')}}".replace(':id', id));
          if ($('#addMemberForm input[name="_method"]').length === 0) {
            $('#addMemberForm').append('<input type="hidden" name="_method" value="PUT">');
          }

          // Update modal title and button
          $('#offcanvasAddMemberLabel').text('{{ __("Edit Member") }}');
          $('#submitMemberBtn').text('{{ __("Update") }}');

          // Show offcanvas
          let memberOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddMember'));
          memberOffcanvas.show();
        },
        error: function (xhr) {
          console.error('Failed to load member:', xhr);
          toastr.error('Could not fetch member data.', 'Error');
        }
      });
    }

    // View Member
    function handleViewMember(id) {
      $.ajax({
        url: "{{ route('members.show', ':id') }}".replace(':id', id),
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#modal-name').text(data.name || '');
          $('#modal-mobile-number').text(data.mobile_number || '');
          $('#created').text(data.created_at || '');
          $("#modalCenter").modal('show');
        },
        error: function (error) {
          console.error('Error:', error);
          toastr.error('Failed to fetch member data.', 'Error');
        }
      });
    }

    // Delete Member
    function deleteMember(url) {
      Swal.fire({
        text: "{{__('Are you sure?')}}",
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

    function handleChatMember(id) {
      // go to routes #chat-history.show with user is id
      window.location.href = "{{ route('chat-history.show', ':id') }}".replace(':id', id);
    }
  </script>
  @if (isset($is_already_visited) && $is_already_visited == false)
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
            title: "{{__('restaurant_job_offers.tour.table_title')}}",
            text: "{{__('restaurant_job_offers.tour.table_text')}}",
            element: '.card-datatable',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_job_offers.tour.search_title')}}",
            text: "{{__('restaurant_job_offers.tour.search_text')}}",
            element: '.dataTables_filter input',
            position: 'bottom',
            skipText: 'Skip',
            nextText: 'Next'
          },
          {
            title: "{{__('restaurant_job_offers.tour.add_title')}}",
            text: "{{__('restaurant_job_offers.tour.add_text')}}",
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
      document.addEventListener('DOMContentLoaded', function () {
        let memberId = '{{ old('member_id') }}';
        // Repopulate form fields with old data
        $('#member-id').val(memberId);
        $('#name').val('{{ old('name') }}');
        $('#mobile_number').val('{{ old('mobile_number') }}');

        // Set form action and modal title/button for add or edit
        if (memberId) {
          // Edit mode
          $('#addMemberForm').attr('action', "{{ route('members.update', ':id') }}".replace(':id', memberId));
          if ($('#addMemberForm input[name="_method"]').length === 0) {
            $('#addMemberForm').append('<input type="hidden" name="_method" value="PUT">');
          }
          $('#offcanvasAddMemberLabel').text('{{ __("Edit Member") }}');
          $('#submitMemberBtn').text('{{ __("Update") }}');
        } else {
          // Add mode
          $('#addMemberForm').attr('action', '{{ route('members.store') }}');
          $('#addMemberForm input[name="_method"]').remove();
          $('#offcanvasAddMemberLabel').text('{{ __("Add Member") }}');
          $('#submitMemberBtn').text('{{ __("Add") }}');
        }
        // Show the offcanvas
        let memberOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddMember'));
        memberOffcanvas.show();
      });
    </script>
  @endif

  <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="ti ti-info-circle me-2"></i>
    <div>{{ __('Members added to Restaurant can create announcements and send promo & offers to customers via WhatsApp.') }}
    </div>
  </div>

  <!-- Users List Table -->
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead class="border-top">
          <tr>
            <th>ID</th>
            <th>{{__('Name')}}</th>
            <th>{{__('Mobile Number')}}</th>
            <th>{{__('action')}}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddMember" aria-labelledby="offcanvasAddMemberLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddMemberLabel" class="offcanvas-title">{{__('Add Member')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form id="addMemberForm" class="pt-0" action="{{ route('members.store') }}" method="POST">
        @csrf
        <input type="hidden" name="member_id" id="member-id">
        <input type="hidden" name="user_id" value="user-id">
        <div class="mb-6">
          <label class="form-label" for="name">{{__('Name')}}</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('e.g. John Doe') }}" required>
          <small class="text-danger">{{ $errors->first('name') }}</small>
        </div>

        <div class="mb-6">
          <label class="form-label" for="mobile_number">{{__('Mobile Number')}}</label>
          <input type="text" class="form-control" id="mobile_number" name="mobile_number"
            placeholder="{{ __('Enter mobile number') }}" required>
          <div>
            <span class="text-info text-small">
              {{ __('Note: The mobile number must be the same as your WhatsApp number and must include the country code first (e.g., 420123456789).') }}
            </span>
          </div>
          <div>
            <small class="text-danger">
              {{ $errors->first('mobile_number') }}
            </small>
          </div>
        </div>

        <button type="submit" id="submitMemberBtn"
          class="btn btn-primary me-3 data-submit btn-custom">{{__('Add')}}</button>
        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">{{__('Cancel')}}</button>
      </form>
    </div>
  </div>

  <!-- Modal for Viewing Member Details -->
  <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">{{ __('View Member') }}</h5>
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
                <span class="text-break m-2" id="modal-name"></span>
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
                <span class="text-break m-2" id="modal-mobile-number"></span>
              </div>
            </li>
            <li class="mb-2 pt-1">
              <div class="d-flex">
                <div class="d-flex h-100">
                  <div class="badge bg-label-primary p-1 m-1 rounded">
                    <i class='ti ti-calendar ti-sm'></i>
                  </div>
                  <span class="fw-semibold me-1 m-2">{{ __('Created at') }}:</span>
                </div>
                <span class="text-break m-2" id="created"></span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
