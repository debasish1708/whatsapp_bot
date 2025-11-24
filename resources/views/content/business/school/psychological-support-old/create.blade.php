@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Psychological Support Setup')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
  ])
  @vite([
    'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
    'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
    'resources/assets/vendor/libs/pickr/pickr-themes.scss'
  ])
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
  @vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/cleavejs/cleave.js',
    'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
  @vite([
    'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
    'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
    'resources/assets/vendor/libs/pickr/pickr.js'
  ])
@endsection

@section('page-style')
  @vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
  ])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/form-layouts.js'])
  {{-- @vite(['resources/assets/js/forms-pickers.js']) --}}
  {{-- @vite([
  'resources/assets/js/extended-ui-sweetalert2.js'
  ]) --}}

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const messageEl = document.getElementById('psychological_support_status_message');

      if (messageEl) {
        const message = messageEl.dataset.message;
        const type = messageEl.dataset.type || 'warning';

        toastr.options = {
          closeButton: true,
          debug: false,
          newestOnTop: true,
          progressBar: false,
          positionClass: 'toast-top-center',
          preventDuplicates: false,
          onclick: null,
          showDuration: 300,
          hideDuration: 1000,
          timeOut: 6000,            // ← Increased from 1000 to 3000
          extendedTimeOut: 1000,    // ← Increased for better visibility
          showEasing: 'swing',
          hideEasing: 'linear',
          showMethod: 'fadeIn',
          hideMethod: 'fadeOut'
        };

        // toastr.warning(message, "Warning");
        // Customize for success type
        if (type === 'success') {
          toastr.options.positionClass = 'toast-top-right';
          toastr.options.progressBar = true;
        }

        else if(type==='error'){
          toastr.options.positionClass = 'toast-top-right';
        }

        // Use dynamic toast type
        if (['success', 'info', 'warning', 'error'].includes(type)) {
          toastr[type](message, type.charAt(0).toUpperCase() + type.slice(1));
        } else {
          toastr.info(message, 'Info');
        }

      }
    });

  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Store flatpickr instances for each input
    const flatpickrInstances = {};

    for (let i = 0; i < 7; i++) {
      const isClosed = document.querySelector(`[name="office_hours[${i}][is_closed]"]`);
      const startInput = document.querySelector(`[name="office_hours[${i}][start_time]"]`);
      const endInput = document.querySelector(`[name="office_hours[${i}][end_time]"]`);

      // Initialize flatpickr for both fields and store instances
      flatpickrInstances[`start_${i}`] = flatpickr(startInput, { enableTime: true, noCalendar: true, dateFormat: 'H:i' });
      flatpickrInstances[`end_${i}`] = flatpickr(endInput, { enableTime: true, noCalendar: true, dateFormat: 'H:i' });

      function toggleTimeFields() {
        if (isClosed.checked) {
          startInput.value = '';
          endInput.value = '';
          startInput.setAttribute('disabled', 'disabled');
          endInput.setAttribute('disabled', 'disabled');
          // Destroy flatpickr so picker cannot be opened
          if (flatpickrInstances[`start_${i}`]) flatpickrInstances[`start_${i}`].destroy();
          if (flatpickrInstances[`end_${i}`]) flatpickrInstances[`end_${i}`].destroy();
        } else {
          startInput.removeAttribute('disabled');
          endInput.removeAttribute('disabled');
          // Re-initialize flatpickr if not present
          if (!startInput._flatpickr) {
            flatpickrInstances[`start_${i}`] = flatpickr(startInput, { enableTime: true, noCalendar: true, dateFormat: 'H:i' });
          }
          if (!endInput._flatpickr) {
            flatpickrInstances[`end_${i}`] = flatpickr(endInput, { enableTime: true, noCalendar: true, dateFormat: 'H:i' });
          }
        }
      }

      // Initial state
      toggleTimeFields();
      // On change
      isClosed.addEventListener('change', toggleTimeFields);
    }

    // Robust form validation
    const form = document.querySelector('#formAuthentication');
    if (form) {
      form.addEventListener('submit', function (e) {
        let hasErrors = false;
        let firstError = null;

        for (let i = 0; i < 7; i++) {
          const isClosed = document.querySelector(`[name="office_hours[${i}][is_closed]"]`);
          const startInput = document.querySelector(`[name="office_hours[${i}][start_time]"]`);
          const endInput = document.querySelector(`[name="office_hours[${i}][end_time]"]`);
          const startError = document.querySelector(`[data-error="office_hours[${i}][start_time]"]`);
          const endError = document.querySelector(`[data-error="office_hours[${i}][end_time]"]`);

          // Clear previous errors
          if (startError) startError.textContent = '';
          if (endError) endError.textContent = '';
          if (startInput) startInput.classList.remove('is-invalid');
          if (endInput) endInput.classList.remove('is-invalid');

          if (isClosed && isClosed.checked) {
            // Not Available: start and end time must be empty
            if (startInput.value && startInput.value.trim() !== '') {
              if (startError) startError.textContent = 'Start time must be empty when service is not available.';
              if (startInput) startInput.classList.add('is-invalid');
              if (!firstError) firstError = startInput;
              hasErrors = true;
            }
            if (endInput.value && endInput.value.trim() !== '') {
              if (endError) endError.textContent = 'End time must be empty when service is not available.';
              if (endInput) endInput.classList.add('is-invalid');
              if (!firstError) firstError = endInput;
              hasErrors = true;
            }
          } else {
            // Available: start and end time must be filled
            if (!startInput.value || startInput.value.trim() === '') {
              if (startError) startError.textContent = 'Start time is required when service is available.';
              if (startInput) startInput.classList.add('is-invalid');
              if (!firstError) firstError = startInput;
              hasErrors = true;
            }
            if (!endInput.value || endInput.value.trim() === '') {
              if (endError) endError.textContent = 'End time is required when service is available.';
              if (endInput) endInput.classList.add('is-invalid');
              if (!firstError) firstError = endInput;
              hasErrors = true;
            }
          }
        }

        if (hasErrors) {
          e.preventDefault();
          if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return false;
        }
      });
    }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function (e) {
    flatpickrTime = document.querySelectorAll('.flatpickr-time1');
    flatpickrTime.flatpickr({
      enableTime: true,
      noCalendar: true
    });
    });
  </script>
@endsection

@section('content')
    @php
      $psychological_support = null;
      if (session()->has('psychological_support_status')) {
        $psychological_support = session('psychological_support_status');
      }
    @endphp

    @if ($psychological_support)
      <div class="d-none" id="psychological_support_status_message"
           data-message="{{ $psychological_support['message'] }}"
           data-type="{{ $psychological_support['type'] }}">
      </div>
    @endif

  @if (session()->exists('psychological_support_profile'))
    @php
    $popup_data = session()->get('psychological_support_profile');
    @endphp
    <div class="d-none" id="updateProfileMessage" data-message="{{ $popup_data['message'] }}"
    data-type="{{ $popup_data['type'] }}">
    </div>
  @endif
  <div class="row">
    <!-- Psychological Support Setup -->
    <div class="col-xxl">
    <div class="card mb-6">
      <h4 class="card-header">Add Psychological Support</h4>
      <form id="formAuthentication" class="card-body" action="{{ route('school.psychological-support.store') }}"
      method="POST" enctype="multipart/form-data">
      @csrf

      <div class="row mb-6">
        <label class="col-sm-3 col-form-label" for="name">Name</label>
        <div class="col-sm-9 mb-3">
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Psychological Support Name"
          value="{{ old('name') }}" />
        </div>
        <small class="text-danger">{{ $errors->first('name') }}</small>
      </div>

      <div class="row mb-6">
        <label class="col-sm-3 col-form-label" for="mobile_number">Mobile Number</label>
        <div class="col-sm-9 mb-3">
        <input type="text" id="mobile_number" name="mobile_number" class="form-control"
          placeholder="Enter Mobile Number" value="{{ old('mobile_number') }}" />
        </div>
        <small class="text-danger">{{ $errors->first('mobile_number') }}</small>
      </div>

      <hr class="my-6 mx-n4" />

      <div class="row mb-6">
        <div class="col-sm-3">
        <span>Day</span>
        </div>
        <div class="col-sm-3 justify-content-center">
        <span>Start Time</span>
        </div>
        <div class="col-sm-3">
        <span>End Time</span>
        </div>
        <div class="col-sm-3">
        <span>Availability</span>
        </div>
      </div>

      @foreach(\App\Enums\SchoolPsychologicalOfficeHour::cases() as $key => $day)
      <div class="row mb-4">
      <label class="col-sm-3 col-form-label">{{ ucwords($day->value) }}</label>

      <div class="col-sm-3 mb-3">
      <input type="hidden" name="office_hours[{{ $key }}][day]" value="{{ $day->value }}" />
      <input type="text" class="form-control flatpickr-time1" placeholder="Start Time"
        name="office_hours[{{ $key }}][start_time]"
        value="{{ old("office_hours.$key.start_time", $timings_by_day[$day]->start_time ?? '') }}" />
      <small class="text-danger" data-error="office_hours[{{ $key }}][start_time]">
        {{ $errors->first("office_hours.$key.start_time") }}
      </small>
      </div>

      <div class="col-sm-3 mb-3">
      <input type="text" class="form-control flatpickr-time1" placeholder="End Time"
        name="office_hours[{{ $key }}][end_time]"
        value="{{ old("office_hours.$key.end_time", $timings_by_day[$day]->end_time ?? '') }}" />
      <small class="text-danger" data-error="office_hours[{{ $key }}][end_time]">
        {{ $errors->first("office_hours.$key.end_time") }}
      </small>
      </div>

      <div class="col-sm-3">
      <div class="d-flex align-items-center gap-2">
        <input class="form-check-input" type="checkbox" id="is_closed_{{ $key }}"
        name="office_hours[{{ $key }}][is_closed]" value="1" {{ old("office_hours.$key.is_closed", $timings_by_day[$day]->is_closed ?? false) ? 'checked' : '' }} />
        <label class="form-check-label" for="is_closed_{{ $key }}">Not Available</label>
      </div>
      <small class="text-danger" data-error="office_hours[{{ $key }}][is_closed]">
        {{ $errors->first("office_hours.$key.is_closed") }}
      </small>
      </div>
      </div>
    @endforeach

      <hr class="my-6 mx-n4" />

      <div class="pt-6">
        <div class="row justify-content-end">
        <div class="w-100 mb-3 d-flex align-items-center justify-content-center">
          <button type="submit" class="btn btn-primary me-4">Save Psychological Support</button>
          <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
        </div>
        </div>
      </div>
      </form>
    </div>
    </div>

  </div>
@endsection