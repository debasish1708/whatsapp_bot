@extends('layouts.layoutMaster')

@section('title', __('Announcements - School Management'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
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

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
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

@section('page-style')
<style>
  .dataTables_filter input {
    height: 35px !important;
  }
  #offcanvasViewAnnouncement .list-group-item {
    background: transparent;
    border: none;
    padding-left: 0;
    padding-right: 0;
  }
  #offcanvasViewAnnouncement .badge {
    font-size: 1em;
  }
</style>
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
                    title: "{{__('announcements.school.announcements_title')}}",
                    text: "{{__('announcements.school.announcements_text')}}",
                    element: '.announcement-list',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('announcements.school.add_title')}}",
                    text: "{{__('announcements.school.add_text')}}",
                    element: '.create-post-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('announcements.school.search_title')}}",
                    text: "{{__('announcements.school.search_text')}}",
                    element: '.dataTables_filter input',
                    position: 'right',
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
      // Initialize date pickers for add form
      const startInput = document.querySelector('#announcement-start-date');
      const endInput = document.querySelector('#announcement-end-date');

      let endPicker = null;

      if (startInput) {
        flatpickr(startInput, {
          enableTime: true,
          dateFormat: 'Y-m-d h:i K',
          minDate: 'today',
          onChange: function (selectedDates) {
            if (selectedDates.length > 0 && endInput) {
              const startDate = selectedDates[0];
              const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000); // +1 day

              if (endPicker) {
                endPicker.set('minDate', minEndDate);
              } else {
                endPicker = flatpickr(endInput, {
                  enableTime: true,
                  dateFormat: 'Y-m-d h:i K',
                  minDate: minEndDate
                });
              }
            }
          }
        });
      }

      if (endInput && !endPicker) {
        endPicker = flatpickr(endInput, {
          enableTime: true,
          dateFormat: 'Y-m-d h:i K',
          minDate: 'today'
        });
      }

      // ----------------------
      // Edit Form Datepickers
      // ----------------------

      const editStartInput = document.querySelector('#edit-announcement-start-date');
      const editEndInput = document.querySelector('#edit-announcement-end-date');

      let editEndPicker = null;

      if (editStartInput) {
        flatpickr(editStartInput, {
          enableTime: true,
          dateFormat: 'Y-m-d h:i K',
          onChange: function (selectedDates) {
            if (selectedDates.length > 0 && editEndInput) {
              const startDate = selectedDates[0];
              const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000); // +1 day

              if (editEndPicker) {
                editEndPicker.set('minDate', minEndDate);
              } else {
                editEndPicker = flatpickr(editEndInput, {
                  enableTime: true,
                  dateFormat: 'Y-m-d h:i K',
                  minDate: minEndDate
                });
              }
            }
          }
        });
      }

      if (editEndInput && !editEndPicker) {
        editEndPicker = flatpickr(editEndInput, {
          enableTime: true,
          dateFormat: 'Y-m-d h:i K',
        });
      }


      var table = $('.datatables-users').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('school.announcement.index') !!}',
        columns: [
          { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
          { data: 'title', name: 'title' },
          { data: 'description', name: 'description' },
          { data: 'start_date', name: 'start_date' },
          { data: 'end_date', name: 'end_date' },
          {
            data: 'type',
            name: 'type',
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
              let typeMap = {
                'general': { title: '{{ __("General") }}', class: 'bg-label-info' },
                'exam': { title: '{{ __("Exam") }}', class: 'bg-label-warning' },
                'holiday': { title: '{{ __("Holiday") }}', class: 'bg-label-success' },
                'substitution': { title: '{{ __("Substitution") }}', class: 'bg-label-primary' },
                'other': { title: '{{ __("Other") }}', class: 'bg-label-secondary' }
              };
              let key = (data || '').toString().toLowerCase();
              let typeObj = typeMap[key];
              return typeObj ? `<span class="badge ${typeObj.class}">${typeObj.title}</span>` : `<span class="badge bg-label-secondary">${data}</span>`;
            }
          },
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
            text: '<i class="ti ti-plus me-1"></i> {{ __("Add Announcement") }}',
            className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
            action: function (e, dt, node, config) {
              $('#addNewUserForm')[0].reset();
              $('#announcement-id').val('');
              $('#announcement-title').val('');
              $('#announcement-description').val('');
              $('#announcement-start-date').val('');
              $('#announcement-end-date').val('');
              $('#announcement-type').select2({
                  width: '100%',
                  dropdownParent: $('#offcanvasAddUser'),
                  placeholder: '{{ __("Select type") }}',
                  allowClear: true
              })
               $('#announcement-type').val('').trigger('change');
              $('#addNewUserForm').attr('action', '{{ route("school.announcement.store") }}').find('input[name="_method"]').remove();
              $('#offcanvasAddUserLabel').text('{{ __("Add Announcement") }}');
              $('#updateLavel').text('{{ __("Save") }}');
              $('.is-invalid').removeClass('is-invalid');
              $('.invalid-feedback').remove();
              const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddUser'));
              offcanvas.show();
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

      $('.dataTables_filter input').attr('placeholder', '{{ __("Search announcements...") }}');

      // Initialize Select2 for Add form
      $('#announcement-type').select2({
        dropdownParent: $('#offcanvasAddUser'),
        placeholder: '{{ __("Select type") }}',
        allowClear: true,
        width: '100%'
      });
      $('#announcement-type').val('{{ old('type') }}').trigger('change');
      // Show error border if not selected
      @if(!$errors->has('type') && !old('type'))
        $('#announcement-type').next('.select2').find('.select2-selection').addClass('is-invalid');
      @endif


    });

  function handleEditAnnouncement(id) {
    $.ajax({
      url: `/school/announcement/${id}/edit`,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        $('#edit-announcement-id').val(data.id);
        $('#edit-announcement-title').val(data.title);
        $('#edit-announcement-description').val(data.description);
        $('#edit-announcement-start-date').val(data.start_date);
        $('#edit-announcement-end-date').val(data.end_date);
        $('#edit-announcement-type').select2({
          dropdownParent: $('#offcanvasEditUser'),
          placeholder: '{{ __("Select type") }}',
          allowClear: true
        }).val(data.type).trigger('change');

        $('#editNewUserForm').attr('action', '{{ route("school.announcement.update", ":id") }}'.replace(':id', id));
        if ($('#editNewUserForm input[name="_method"]').length === 0) {
          $('#editNewUserForm').prepend('<input type="hidden" name="_method" value="PUT">');
        }
        $('#offcanvasEditUserLabel').text('{{ __("Edit Announcement") }}');
        $('#editUpdateLavel').text('{{ __("Update") }}').attr('data-loading-text', '{{ __("Updating...") }}');

        // Clear any previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEditUser'));
        offcanvas.show();
      },
      error: function (error) {
        console.error('Error:', error);
        toastr.error('{{ __("Failed to fetch announcement data.") }}', '{{ __("Error") }}');
      }
    });
  }

  // view announcement
  function handleViewAnnouncement(id) {
  $.ajax({
    url: `/school/announcement/${id}`,
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      let typeMap = {
        'general': { title: '{{ __("General") }}', class: 'bg-label-info' },
        'exam': { title: '{{ __("Exam") }}', class: 'bg-label-warning' },
        'holiday': { title: '{{ __("Holiday") }}', class: 'bg-label-success' },
        'substitution': { title: '{{ __("Substitution") }}', class: 'bg-label-primary' },
        'other': { title: '{{ __("Other") }}', class: 'bg-label-secondary' }
      };
      let typeKey = (data.type || '').toLowerCase();
      let typeObj = typeMap[typeKey];
      let typeBadge = typeObj
        ? `<span class="badge ${typeObj.class}">${typeObj.title}</span>`
        : `<span class="badge bg-label-secondary">${data.type}</span>`;

      // Populate values
      // $('#announcementTitle').text(data.title || '-');
      // $('#announcementDescription').text(data.description || '-');
      // $('#announcementStartDate').text((data.start_date || '-').substring(0, 16));
      // $('#announcementEndDate').text((data.end_date || '-').substring(0, 16));
      // $('#announcementEndDate').text(data.end_date || '-');
      // $('#announcementTypeBadge').html(typeBadge);
      // const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasViewAnnouncement'));
      // offcanvas.show();

      $('#modal-title').text(data.title || '');
      $('#modal-description').text(data.description || '');
      $('#modal-valid-from').text(data.start_date);
      $('#modal-valid-to').text(data.end_date);
      $('#modal-type').html(typeBadge);
      $('#created').text(data.created_at || '');

      $("#modalCenter").modal('show');
    },
    error: function (error) {
      console.error('Error:', error);
      toastr.error('{{ __("Failed to fetch announcement data.") }}', '{{ __("Error") }}');
    }
  });
}

    function deleteAnnoucement(url){
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

  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Check if we're editing (has announcement_id) or adding new
        @if(old('announcement_id'))
          // Editing - show edit offcanvas
          $('#edit-announcement-id').val('{{ old('announcement_id') }}');
          $('#edit-announcement-title').val('{{ old('title') }}');
          $('#edit-announcement-description').val('{{ old('description') }}');
          $('#edit-announcement-start-date').val('{{ old('start_date') }}');
          $('#edit-announcement-end-date').val('{{ old('end_date') }}');
          // Initialize Select2 for edit form with error
          $('#edit-announcement-type').select2({
            dropdownParent: $('#offcanvasEditUser'),
            placeholder: '{{ __("Select type") }}',
            allowClear: true,
            width: '100%'
          }).val('{{ old('type') }}').trigger('change');
          $('#editNewUserForm').attr('action', '{{ route("school.announcement.update", old('announcement_id')) }}');
          if ($('#editNewUserForm input[name="_method"]').length === 0) {
            $('#editNewUserForm').prepend('<input type="hidden" name="_method" value="PUT">');
          }
          $('#offcanvasEditUserLabel').text('{{ __("Edit Announcement") }}');
          $('#editUpdateLavel').text('{{ __("Update") }}');
          const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEditUser'));
          offcanvas.show();
        @else
          // Adding new - show add offcanvas
          $('#announcement-title').val('{{ old('title') }}');
          $('#announcement-description').val('{{ old('description') }}');
          $('#announcement-start-date').val('{{ old('start_date') }}');
          $('#announcement-end-date').val('{{ old('end_date') }}');
          // Initialize Select2 for add form with error
          $('#announcement-type').val('{{ old('type') }}').trigger('change');
          // Show error border if not selected
          @if(!$errors->has('type') && !old('type'))
            $('#announcement-type').next('.select2').find('.select2-selection').addClass('is-invalid');
          @endif
          const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddUser'));
          offcanvas.show();
          setTimeout(() => {
            $('#announcement-type').select2('destroy').select2({
              width: '100%',
              dropdownParent: $('#offcanvasAddUser .offcanvas-body'),
              placeholder: '{{ __("Select type") }}',
              allowClear: true,
              width: '100%'
            }).val('{{ old('type') }}').trigger('change');
          }, 50);
        @endif
      });
    </script>
  @endif

<div class="card announcement-list">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>{{ __('title') }}</th>
          <th>{{ __('description') }}</th>
          <th>{{ __('Valid From') }}</th>
          <th>{{ __('Valid To') }}</th>
          <th>{{ __('Type') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

  <!-- Add Announcement Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{ __('Add Announcement') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-4">
      <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('school.announcement.store') }}" method="post">
        @csrf
        <div class="mb-3">
          <label class="form-label" for="announcement-title">{{ __('title') }}</label>
          <input type="text" class="form-control @error('title') is-invalid @enderror" id="announcement-title" placeholder="{{ __('Enter announcement title') }}" name="title" value="{{ old('title') }}" required/>
          @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="announcement-description">{{ __('description') }}</label>
          <textarea class="form-control @error('description') is-invalid @enderror" id="announcement-description" placeholder="{{ __('Enter announcement description') }}" name="description" rows="3" required>{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="announcement-start-date">{{ __('Valid From') }}</label>
          <input type="text" class="form-control @error('start_date') is-invalid @enderror" name="start_date" placeholder="YYYY-MM-DD HH:MM" id="announcement-start-date" value="{{ old('start_date') }}" required />
          @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="announcement-end-date">{{ __('Valid To') }}</label>
          <input type="text" class="form-control @error('end_date') is-invalid @enderror" name="end_date" placeholder="YYYY-MM-DD HH:MM" id="announcement-end-date" value="{{ old('end_date') }}" required />
          @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="announcement-type">{{ __('Type') }}</label>
          @php
            $announcement_types=config('constant.school_announcements');
          @endphp
          <select id="announcement-type" class="select2 form-select form-select @error('type') is-invalid @enderror" data-allow-clear="true" name="type" style="width: 100%;" required>
            <option value="">{{ __('Select type') }}</option>
            @foreach (array_keys(config('constant.school_announcements')) as $key)
              <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ __(ucfirst($key)) }}</option>
            @endforeach
          </select>
          @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom"
        data-loading-text="{{ __('Saving...') }}"
        onclick="this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.submit();">{{ __('Save') }}</button>
        <button type="reset" id="cancelButton" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

  <!-- Edit Announcement Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasEditUserLabel" class="offcanvas-title">{{ __('Edit Announcement') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-4">
      <form class="edit-new-user pt-0" id="editNewUserForm" action="" method="post">
        @csrf
        <input type="hidden" name="announcement_id" id="edit-announcement-id" value="{{ old('announcement_id') }}">
        <div class="mb-3">
          <label class="form-label" for="edit-announcement-title">{{ __('title') }}</label>
          <input type="text" class="form-control @error('title') is-invalid @enderror" id="edit-announcement-title" placeholder="{{ __('Enter announcement title') }}" name="title" value="{{ old('title') }}" required/>
          @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="edit-announcement-description">{{ __('description') }}</label>
          <textarea class="form-control @error('description') is-invalid @enderror" id="edit-announcement-description" placeholder="{{ __('Enter announcement description') }}" name="description" rows="3" required>{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="edit-announcement-start-date">{{ __('Valid From') }}</label>
          <input type="text" class="form-control @error('start_date') is-invalid @enderror" name="start_date" placeholder="YYYY-MM-DD HH:MM" id="edit-announcement-start-date" value="{{ old('start_date') }}" required />
          @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="edit-announcement-end-date">{{ __('Valid To') }}</label>
          <input type="text" class="form-control @error('end_date') is-invalid @enderror" name="end_date" placeholder="YYYY-MM-DD HH:MM" id="edit-announcement-end-date" value="{{ old('end_date') }}" required />
          @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label" for="edit-announcement-type">{{ __('Type') }}</label>
          @php
            $announcement_types=config('constant.school_announcements');
          @endphp
          <select id="edit-announcement-type" class="select2 form-select form-select @error('type') is-invalid @enderror" data-allow-clear="true" name="type" required>
            <option value="">{{ __('Select type') }}</option>
            @foreach (array_keys(config('constant.school_announcements')) as $key)
              <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ __(ucfirst($key)) }}</option>
            @endforeach
          </select>
          @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" id="editUpdateLavel" class="btn btn-primary me-3 data-submit btn-custom"
        data-loading-text="{{ __('Updating...') }}"
        onclick="this.disabled = true; this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>' + this.dataset.loadingText; this.form.submit();">{{ __('Update') }}</button>
        <button type="reset" id="editCancelButton" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
      </form>
    </div>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasViewAnnouncement" aria-labelledby="offcanvasViewAnnouncementLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasViewAnnouncementLabel" class="offcanvas-title">{{ __('View Announcement') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-4">
      <div class="card shadow-none border-0 mb-0">
        <div class="card-body p-0">
          <ul class="list-group list-group-flush border-0">
            <li class="list-group-item border-0 py-0 mb-1">
              <strong><i class="ti ti-bell me-2"></i>{{ __('title') }}:</strong> <span id="announcementTitle"></span>
            </li>
            <li class="list-group-item border-0 py-0 mb-1">
              <strong><i class="ti ti-align-left me-2"></i>{{ __('description') }}:</strong>
              <div id="announcementDescription" style="white-space: pre-line;"></div>
            </li>
            <li class="list-group-item border-0 py-0 mb-1">
              <strong><i class="ti ti-calendar-event me-2"></i>{{ __('Valid From') }}:</strong> <span id="announcementStartDate"></span>
            </li>
            <li class="list-group-item border-0 py-0 mb-1">
              <strong><i class="ti ti-calendar-event me-2"></i>{{ __('Valid To') }}:</strong> <span id="announcementEndDate"></span>
            </li>
            <li class="list-group-item border-0 py-0 mb-1">
              <strong><i class="ti ti-tag me-2"></i>{{ __('Type') }}:</strong> <span id="announcementTypeBadge"></span>
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
                      <h5 class="modal-title" id="modalCenterTitle">{{ __('Annoucement Details') }}</h5>
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
                                      <span class="fw-semibold me-1 m-2">{{__('title')}}:</span>
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
                                      <span class="fw-semibold me-1 m-2">{{__('description')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-description"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('valid from')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-valid-from"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('valid to')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-valid-to"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-tag ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('type')}}:</span>
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
