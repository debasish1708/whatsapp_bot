@extends('layouts.layoutMaster')

@section('title', __('Event Calendar'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/quill/editor.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  ])
  @vite([
    'resources/assets/vendor/libs/toastr/toastr.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss'
  ])
   @vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
  <style>
    .modal-dialog-top {
    margin-top: 2rem;
    }

    /* Custom styles for the calendar layout */
    .app-calendar-sidebar {
      min-height: 400px;
    }

    @media (max-width: 991.98px) {
      .app-calendar-sidebar {
        position: absolute;
        z-index: 1000;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 300px;
      }

      .app-calendar-content {
        width: 100%;
      }
    }

    .event-filters {
      max-height: 60vh;
      overflow-y: auto;
    }
  </style>
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js',
  ])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
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
                    title: "{{__('events.school.tour.events_title')}}",
                    text: "{{__('events.school.tour.events_text')}}",
                    element: '.app-calendar-wrapper',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('events.school.tour.filter_title')}}",
                    text: "{{__('events.school.tour.filter_text')}}",
                    element: '.fc-button-group',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('events.school.tour.add_title')}}",
                    text: "{{__('events.school.tour.add_text')}}",
                    element: '.add-btn',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "{{__('events.school.tour.filter_events_title')}}",
                    text: "{{__('events.school.tour.filter_events_text')}}",
                    element: '.event-filters',
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

  {{-- IMPORTANT: Define translations FIRST before loading calendar scripts --}}
  <script>
    // Expose current Laravel locale to JS so FullCalendar/Flatpickr can use it
    window.appLocale = '{{ app()->getLocale() }}';

    // Define all translations BEFORE calendar scripts load
    window.translations = {!! json_encode([
      'Add' => __('Add'),
      'Update' => __('Update'),
      'Delete' => __('Delete'),
      'Cancel' => __('Cancel'),
      'Adding...' => __('Adding...'),
      'Updating...' => __('Updating...'),
      'Event created successfully!' => __('Event created successfully!'),
      'Failed to create event' => __('Failed to create event'),
      'Event updated successfully!' => __('Event updated successfully!'),
      'Failed to update event' => __('Failed to update event'),
      'Event deleted successfully!' => __('Event deleted successfully!'),
      'Failed to delete event' => __('Failed to delete event'),
      'Please enter event title' => __('Please enter event title'),
      'Please enter start date' => __('Please enter start date'),
      'Please enter end date' => __('Please enter end date'),
      'Update Event' => __('Update Event'),
      'Add Event' => __('Add Event'),
      'Select value' => __('Select value'),
      'Select type' => __('Select type'),
      'View All' => __('View All'),
      'Event Filters' => __('Event Filters'),
      'Event Type' => __('Event Type'),
      'Start Date' => __('Start Date'),
      'End Date' => __('End Date'),
      'Description' => __('Description'),
      'Title' => __('Title'),
      'Are you sure?' => __('Are you sure?'),
      'Yes' => __('Yes'),
      'Cannot create events for past dates' => __('Cannot create events for past dates')
    ]) !!};

    window.calendarsColor = @json(
      collect(\App\Enums\SchoolEvents::cases())->mapWithKeys(fn($e) => [
        $e->value => $e->label()
      ])
    );

    window.eventTypeColors = @json(
      collect(\App\Enums\SchoolEvents::cases())->mapWithKeys(fn($event) => [
        $event->value => $event->hexColor()
      ])
    );
  </script>

  {{-- Load calendar scripts AFTER translations are defined --}}
  @vite([
    'resources/js/event-calender-events.js',
    'resources/js/event-calender.js',
  ])

  {{-- Delete event function --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Global deleteEvent function used by the calendar delete button
      window.deleteEvent = function(url) {
        Swal.fire({
          text: window.translations['Are you sure?'] || 'Are you sure?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: window.translations['Yes'] || 'Yes',
          cancelButtonText: window.translations['Cancel'] || 'Cancel',
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
                  title: "Success",
                  text: result.message,
                  showConfirmButton: false,
                  timer: 1500,
                  customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                  },
                  buttonsStyling: false
                });

                if (typeof calendar !== 'undefined') {
                  calendar.refetchEvents();
                }

                const addEventSidebar = document.getElementById('addEventSidebar');
                if (addEventSidebar) {
                  const bsAddEventSidebar = bootstrap.Offcanvas.getInstance(addEventSidebar);
                  if (bsAddEventSidebar) {
                    bsAddEventSidebar.hide();
                  }
                }
              },
              error: function (error) {
                Swal.fire({
                  icon: 'error',
                  title: "Error",
                  text: error.responseJSON?.message || 'Failed to delete event',
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
    });
  </script>
@endsection

@section('content')

  @if ($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);
    bsAddEventSidebar.show();
    });
    </script>
  @endif

  <div class="card app-calendar-wrapper">
    <div class="row g-0">
      <!-- Mobile sidebar toggle button -->
      <div class="d-lg-none w-100 p-3 border-bottom">
        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#app-calendar-sidebar" aria-expanded="false" aria-controls="app-calendar-sidebar">
          <i class="ti ti-filter me-2"></i>
          {{ __('Toggle Filters') }}
        </button>
      </div>
    <!-- Calendar Sidebar -->
    <div class="col-md-3 app-calendar-sidebar border-end collapse d-lg-block" id="app-calendar-sidebar">
      <div class="border-bottom p-4 my-sm-0 mb-3">
      <button class="btn btn-primary w-100 data-submit btn-custom add-btn" data-bs-toggle="offcanvas"
        data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
        <i class="ti ti-plus ti-16px me-2"></i>
        <span class="align-middle">{{ __('Add Event') }}</span>
      </button>
      </div>
      <div class="px-4 mt-4 pb-2 event-filters">
      <!-- Filter -->
      <div>
        <h5>{{ __('Event Filters') }}</h5>
      </div>

      <div class="form-check form-check-secondary mb-3 ms-2">
        <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all" checked>
        <label class="form-check-label" for="selectAll">{{ __('View All') }}</label>
      </div>

      <div class="app-calendar-events-filter text-heading">
        @foreach (\App\Enums\SchoolEvents::cases() as $event)
          <div class="form-check form-check-{{ $event->label() }} mb-3 ms-2">
            <input class="form-check-input input-filter" type="checkbox"
              id="select-{{ str_replace('_', '-', $event->value) }}" data-value="{{ $event->value }}" checked>
            <label class="form-check-label" for="select-{{ str_replace('_', '-', $event->value) }}">
              {{ $event->displayName() }}
            </label>
          </div>
        @endforeach
      </div>

      </div>
    </div>
    <!-- /Calendar Sidebar -->

    <!-- Calendar & Modal -->
    <div class="col-12 col-md-9 app-calendar-content">
      <div class="card shadow-none border-0">
      <div class="card-body pb-0">
        <!-- FullCalendar -->
        <div id="calendar"></div>
      </div>
      </div>
      <div class="app-overlay"></div>
      <!-- FullCalendar Offcanvas -->
      <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
      aria-labelledby="addEventSidebarLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="addEventSidebarLabel">{{ __('events.school.add_event') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <form class="event-form pt-0" id="eventForm">
        @csrf
        <div class="mb-5">
          <label class="form-label" for="eventTitle">{{ __('Title') }}</label>
          <input type="text" class="form-control" id="eventTitle" name="title" placeholder="{{ __('Title') }}" required />
          <div id="eventTitleError" class="invalid-feedback" style="display:none"></div>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventLabel">{{ __('Event Type') }}</label>
          <select class="select2 select-event-label form-select" id="eventLabel" name="type" required>
          <option value="">{{ __('Select type') }}</option>
          @foreach (\App\Enums\SchoolEvents::cases() as $event)
            <option data-label="{{ $event->label() }}" value="{{ __($event->value) }}" {{ old('type', $eventValue ?? '') === $event->value ? 'selected' : '' }}>
              {{ $event->displayName() }}
            </option>
          @endforeach
          </select>
          <div id="eventLabelError" class="invalid-feedback" style="display:none"></div>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventStartDate">{{ __('Start Date') }}</label>
          <input type="text" class="form-control" id="eventStartDate" name="start_date" placeholder="{{ __('Start Date') }}" required />
          <div id="eventStartDateError" class="invalid-feedback" style="display:none"></div>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventEndDate">{{ __('End Date') }}</label>
          <input type="text" class="form-control" id="eventEndDate" name="end_date" placeholder="{{ __('End Date') }}" required />
          <div id="eventEndDateError" class="invalid-feedback" style="display:none"></div>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventDescription">{{ __('Description') }}</label>
          <textarea class="form-control" name="description" id="eventDescription"></textarea>
          <div id="eventDescriptionError" class="invalid-feedback" style="display:none"></div>
        </div>
        <div class="d-flex justify-content-sm-between justify-content-start mt-6 gap-2">
          <div class="d-flex">

          <button type="submit" id="addEventBtn" class="btn btn-primary me-3 data-submit btn-custom">{{ __('Add') }}</button>
          <button type="button" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
            data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
          </div>
          <button type="button" class="btn btn-label-danger btn-delete-event d-none">{{ __('Delete') }}</button>
        </div>
        </form>
      </div>
      </div>
    </div>
    <!-- /Calendar & Modal -->
    </div>
  </div>
@endsection
