/**
 * App Calendar
 */

/**
 * ! If both start and end dates are same Full calendar will nullify the end date value.
 * ! Full calendar will end the event on a day before at 12:00:00AM thus, event won't extend to the end date.
 * ! We are getting events from a separate file named app-calendar-events.js. You can add or remove events from there.
 *
 **/

'use strict';

let direction = 'ltr';

if (isRtl) {
  direction = 'rtl';
}

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const calendarEl = document.getElementById('calendar'),
      appCalendarSidebar = document.querySelector('.app-calendar-sidebar'),
      addEventSidebar = document.getElementById('addEventSidebar'),
      appOverlay = document.querySelector('.app-overlay'),
      calendarsColor = window.calendarsColor, // ✅ loaded from Laravel,
      offcanvasTitle = document.querySelector('.offcanvas-title'),
      btnToggleSidebar = document.querySelector('.btn-toggle-sidebar'),
      btnSubmit = document.querySelector('#addEventBtn'),
      btnDeleteEvent = document.querySelector('.btn-delete-event'),
      btnCancel = document.querySelector('.btn-cancel'),
      eventTitle = document.querySelector('#eventTitle'),
      eventStartDate = document.querySelector('#eventStartDate'),
      eventEndDate = document.querySelector('#eventEndDate'),
      eventUrl = document.querySelector('#eventURL'),
      eventLabel = $('#eventLabel'), // ! Using jquery vars due to select2 jQuery dependency
      eventGuests = $('#eventGuests'), // ! Using jquery vars due to select2 jQuery dependency
      eventLocation = document.querySelector('#eventLocation'),
      eventDescription = document.querySelector('#eventDescription'),
      allDaySwitch = document.querySelector('.allDay-switch'),
      selectAll = document.querySelector('.select-all'),
      filterInput = [].slice.call(document.querySelectorAll('.input-filter'));

    let eventToUpdate,
      currentEvents = events, // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events
      isFormValid = false;

    // Init event Offcanvas
    const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

    //! TODO: Update Event label and guest code to JS once select removes jQuery dependency
    // Event Label (select2)
    if (eventLabel.length) {
      function renderBadges(option) {
        if (!option.id) {
          return option.text;
        }
        var $badge =
          "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text;

        return $badge;
      }
      eventLabel.wrap('<div class="position-relative"></div>').select2({
        placeholder: window.translations['Select value'] || 'Select value',
        dropdownParent: eventLabel.parent(),
        templateResult: renderBadges,
        templateSelection: renderBadges,
        minimumResultsForSearch: -1,
        escapeMarkup: function (es) {
          return es;
        }
      });
    }

    // Event Guests (select2)
    if (eventGuests.length) {
      function renderGuestAvatar(option) {
        if (!option.id) {
          return option.text;
        }
        var $avatar =
          "<div class='d-flex flex-wrap align-items-center'>" +
          "<div class='avatar avatar-xs me-2'>" +
          "<img src='" +
          assetsPath +
          'img/avatars/' +
          $(option.element).data('avatar') +
          "' alt='avatar' class='rounded-circle' />" +
          '</div>' +
          option.text +
          '</div>';

        return $avatar;
      }
      eventGuests.wrap('<div class="position-relative"></div>').select2({
        placeholder: window.translations['Select value'] || 'Select value',
        dropdownParent: eventGuests.parent(),
        closeOnSelect: false,
        templateResult: renderGuestAvatar,
        templateSelection: renderGuestAvatar,
        escapeMarkup: function (es) {
          return es;
        }
      });
    }

    // Event start (flatpicker)
    if (eventStartDate) {
      var start = eventStartDate.flatpickr({
        enableTime: true,
        altInput: true,
        altFormat: 'Y-m-d h:i K', // Display format
        dateFormat: 'Y-m-d h:i K', // Value format
        minDate: null,
        locale:
          window.appLocale === 'cs'
            ? typeof Flatpickr !== 'undefined' && Flatpickr.l10ns && Flatpickr.l10ns.cs
              ? Flatpickr.l10ns.cs
              : 'cs'
            : 'en',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) {
            instance.mobileInput.setAttribute('step', null);
          }
        },
        onChange: function (selectedDates, dateStr) {
          // Update end date min date when start date changes
          if (end) {
            end.set('minDate', selectedDates[0]);
          }
        }
      });
    }

    // Event end (flatpicker)
    if (eventEndDate) {
      var end = eventEndDate.flatpickr({
        enableTime: true,
        altInput: true,
        altFormat: 'Y-m-d h:i K', // Display format
        dateFormat: 'Y-m-d h:i K', // Value format
        minDate: null,
        locale:
          window.appLocale === 'cs'
            ? typeof Flatpickr !== 'undefined' && Flatpickr.l10ns && Flatpickr.l10ns.cs
              ? Flatpickr.l10ns.cs
              : 'cs'
            : 'en',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) {
            instance.mobileInput.setAttribute('step', null);
          }
        }
      });
    }

    // Utility to check if a date is in the past (ignores time)
    function isPast(date) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const d = new Date(date);
      d.setHours(0, 0, 0, 0);
      return d < today;
    }

    // Event click function
    function eventClick(info) {
      eventToUpdate = info.event;

      bsAddEventSidebar.show();

      // For update event set offcanvas title text: Update Event
      if (offcanvasTitle) {
        offcanvasTitle.innerHTML = window.translations['Update Event'] || 'Update Event';
      }
      btnSubmit.innerHTML = window.translations['Update'] || 'Update';
      btnSubmit.classList.add('btn-update-event');
      btnSubmit.classList.remove('btn-add-event');
      btnDeleteEvent.classList.remove('d-none');

      // Fill form with event data
      eventTitle.value = eventToUpdate.title;

      // Set start date
      if (eventToUpdate.start) {
        start.setDate(eventToUpdate.start, true, 'Y-m-d h:i K');
      }

      // Set end date - handle both cases where end date might be null
      if (eventToUpdate.end) {
        end.setDate(eventToUpdate.end, true, 'Y-m-d h:i K');
      } else if (eventToUpdate.start) {
        end.setDate(eventToUpdate.start, true, 'Y-m-d h:i K');
      }

      // Set all day switch - only if the element exists
      if (allDaySwitch) {
        allDaySwitch.checked = eventToUpdate.allDay === true;
      }

      // Set event type
      eventLabel.val(eventToUpdate.extendedProps.calendar).trigger('change');

      // Set description - properly handle the description from extendedProps
      if (eventDescription) {
        const description = eventToUpdate.extendedProps?.description || '';
        eventDescription.value = description;
      }

      // --- Disable/enable fields based on date logic ---
      // Always enable everything first
      eventStartDate.removeAttribute('disabled');
      eventEndDate.removeAttribute('disabled');
      eventTitle.removeAttribute('disabled');
      eventLabel.prop('disabled', false);
      eventDescription.removeAttribute('disabled');
      btnSubmit.style.display = '';
      btnSubmit.removeAttribute('disabled'); // Enable update button by default
      btnDeleteEvent.style.display = '';

      // Get start and end date values
      const startDateValue = eventToUpdate.start ? new Date(eventToUpdate.start) : null;
      const endDateValue = eventToUpdate.end ? new Date(eventToUpdate.end) : null;
      const now = new Date();
      now.setHours(0, 0, 0, 0);
      const isStartPast = startDateValue && startDateValue < now;
      const isEndPast = endDateValue && endDateValue < now;

      // If start is past, disable start date
      if (isStartPast && !isEndPast) {
        eventStartDate.setAttribute('disabled', 'disabled');
      }

      // If end date is not in the past, set minDate of end picker to today
      if (!isEndPast && typeof end !== 'undefined' && end) {
        end.set('minDate', now);
      } else if (typeof end !== 'undefined' && end) {
        end.set('minDate', null);
      }

      // If both start and end are past, disable all fields and disable update button
      if (isStartPast && isEndPast) {
        eventStartDate.setAttribute('disabled', 'disabled');
        eventEndDate.setAttribute('disabled', 'disabled');
        eventTitle.setAttribute('disabled', 'disabled');
        eventLabel.prop('disabled', true);
        eventDescription.setAttribute('disabled', 'disabled');
        btnSubmit.setAttribute('disabled', 'disabled'); // Make update button unclickable
        btnDeleteEvent.style.display = '';
      }

      // If only end date is past, disable start and end date fields
      if (!isStartPast && isEndPast) {
        eventStartDate.setAttribute('disabled', 'disabled');
        eventEndDate.setAttribute('disabled', 'disabled');
      }

      // Log the event data for debugging
      // console.log('Event Data:', {
      //   title: eventToUpdate.title,
      //   start: eventToUpdate.start,
      //   end: eventToUpdate.end,
      //   type: eventToUpdate.extendedProps.calendar,
      //   description: eventToUpdate.extendedProps?.description || '',
      //   extendedProps: eventToUpdate.extendedProps
      // });
    }

    // Modify sidebar toggler
    function modifyToggler() {
      const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
      fcSidebarToggleButton.classList.remove('fc-button-primary');
      fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
      while (fcSidebarToggleButton.firstChild) {
        fcSidebarToggleButton.firstChild.remove();
      }
      fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
      fcSidebarToggleButton.setAttribute('data-overlay', '');
      fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
      fcSidebarToggleButton.insertAdjacentHTML('beforeend', '<i class="ti ti-menu-2 ti-lg text-heading"></i>');
    }

    // Filter events by calender
    function selectedCalendars() {
      let selected = [],
        filterInputChecked = [].slice.call(document.querySelectorAll('.input-filter:checked'));

      filterInputChecked.forEach(item => {
        selected.push(item.getAttribute('data-value'));
      });

      return selected;
    }

    // Handle select all checkbox
    selectAll.addEventListener('change', function () {
      filterInput.forEach(item => {
        item.checked = selectAll.checked;
      });
      calendar.refetchEvents();
    });

    // Handle individual filter checkboxes
    filterInput.forEach(item => {
      item.addEventListener('change', function () {
        // If any checkbox is unchecked, uncheck "View All"
        if (!this.checked) {
          selectAll.checked = false;
        }
        // If all checkboxes are checked, check "View All"
        if ([].slice.call(filterInput).every(checkbox => checkbox.checked)) {
          selectAll.checked = true;
        }
        calendar.refetchEvents();
      });
    });

    // --------------------------------------------------------------------------------------------------
    // AXIOS: fetchEvents
    // * This will be called by fullCalendar to fetch events. Also this can be used to refetch events.
    // --------------------------------------------------------------------------------------------------
    function fetchEvents(info, successCallback) {
      $.ajax({
        url: '/school/event-calender',
        method: 'GET',
        data: {
          start: info.startStr,
          end: info.endStr
        },
        success: function (result) {
          // Filter events based on selected calendars
          const selectedTypes = selectedCalendars();
          console.log('Selected types:', selectedTypes);
          console.log('All events:', result);

          const filteredEvents = result.filter(event => {
            const eventType = event.extendedProps.calendar;
            const isIncluded =
              selectedTypes.includes(eventType) ||
              selectedTypes.some(selectedType => selectedType.toLowerCase() === eventType.toLowerCase());
            console.log(`Event "${event.title}" with type "${eventType}" included:`, isIncluded);
            return isIncluded;
          });

          console.log('Filtered events:', filteredEvents);

          // Format events for display
          const formattedEvents = filteredEvents.map(event => {
            return {
              ...event,
              backgroundColor: getEventColor(event.extendedProps.calendar),
              borderColor: getEventColor(event.extendedProps.calendar),
              textColor: '#fff',
              display: 'block',
              extendedProps: {
                ...event.extendedProps,
                description: event.extendedProps.description || ''
              }
            };
          });

          successCallback(formattedEvents);
        },
        error: function (error) {
          toastr.error('Failed to fetch events');
          console.error('Failed to fetch events:', error);
        }
      });
    }

    // Get color based on event type
    function getEventColor(type) {
      return window.eventTypeColors?.[type] || '#d8d3ff'; // default fallback color
    }

    // Init FullCalendar
    // ------------------------------------------------
    // Guard against duplicate calendar instances (e.g., hot reloads)
    if (window.calendar && typeof window.calendar.destroy === 'function') {
      try {
        window.calendar.destroy();
      } catch (e) {}
    }

    let calendar = new Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: window.appLocale === 'cs' ? 'cs' : 'en',
      events: fetchEvents,
      eventContent: function (arg) {
        return {
          html: `
            <div class="fc-event-main-frame">
              <div class="fc-event-title-container">
                <div class="fc-event-title fc-sticky">
                  <i class="fas fa-calendar-alt me-1"></i>
                  <span class="event-title">${arg.event.extendedProps.name}</span>
                </div>
              </div>
            </div>
          `
        };
      },
      plugins: [dayGridPlugin, interactionPlugin, listPlugin, timegridPlugin],
      editable: true,
      dragScroll: true,
      dayMaxEvents: 2,
      eventResizableFromStart: true,
      headerToolbar: {
        start: 'sidebarToggle, prev, next, title',
        end: 'dayGridMonth,timeGridWeek,listMonth'
      },
      direction: direction,
      initialDate: new Date(),
      navLinks: true,
      eventClick: eventClick,
      dateClick: function (info) {
        // Check if the clicked date is in the past
        const clickedDate = new Date(info.date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        clickedDate.setHours(0, 0, 0, 0);

        // If clicked date is before today, don't open the form
        if (clickedDate < today) {
          toastr.warning(window.translations['Cannot create events for past dates'] || 'Cannot create events for past dates');
          return;
        }

        // Only proceed if date is today or future
        resetValues();
        start.setDate(info.date, true, 'Y-m-d');
        end.setDate(info.date, true, 'Y-m-d');
        bsAddEventSidebar.show();
      },
      eventDrop: function (info) {
        const eventData = {
          id: info.event.id,
          title: info.event.title,
          type: info.event.extendedProps.calendar,
          start_date: info.event.start.toISOString(),
          end_date: info.event.end ? info.event.end.toISOString() : info.event.start.toISOString(),
          description: info.event.extendedProps.description
        };
        updateEvent(eventData);
      },
      eventResize: function (info) {
        const eventData = {
          id: info.event.id,
          title: info.event.title,
          type: info.event.extendedProps.calendar,
          start_date: info.event.start.toISOString(),
          end_date: info.event.end.toISOString(),
          description: info.event.extendedProps.description
        };
        updateEvent(eventData);
      }
    });

    // Make calendar globally accessible
    window.calendar = calendar;

    // Render calendar
    calendar.render();
    // Modify sidebar toggler
    modifyToggler();

    const eventForm = document.getElementById('eventForm');
    // const fv = FormValidation.formValidation(eventForm, {
    //   fields: {
    //     eventTitle: {
    //       validators: {
    //         notEmpty: {
    //           message: 'Please enter event title '
    //         }
    //       }
    //     },
    //     eventStartDate: {
    //       validators: {
    //         notEmpty: {
    //           message: 'Please enter start date '
    //         }
    //       }
    //     },
    //     eventEndDate: {
    //       validators: {
    //         notEmpty: {
    //           message: 'Please enter end date '
    //         }
    //       }
    //     }
    //   },
    //   plugins: {
    //     trigger: new FormValidation.plugins.Trigger(),
    //     bootstrap5: new FormValidation.plugins.Bootstrap5({
    //       // Use this for enabling/changing valid/invalid class
    //       eleValidClass: '',
    //       rowSelector: function (field, ele) {
    //         // field is the field name & ele is the field element
    //         return '.mb-5';
    //       }
    //     }),
    //     submitButton: new FormValidation.plugins.SubmitButton(),
    //     // Submit the form when all fields are valid
    //     // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
    //     autoFocus: new FormValidation.plugins.AutoFocus()
    //   }
    // })
    //   .on('core.form.valid', function () {
    //     // Jump to the next step when all fields in the current step are valid
    //     isFormValid = true;
    //   })
    //   .on('core.form.invalid', function () {
    //     // if fields are invalid
    //     isFormValid = false;
    //   });

    // Sidebar Toggle Btn
    if (btnToggleSidebar) {
      btnToggleSidebar.addEventListener('click', e => {
        // Reset all values when opening for new event
        resetValues();

        // Show the offcanvas for adding new event
        bsAddEventSidebar.show();

        if (offcanvasTitle) {
          offcanvasTitle.innerHTML = window.translations['Add Event'] || 'Add Event';
        }
        if (btnSubmit) {
          btnSubmit.innerHTML = window.translations['Add'] || 'Add';
        }
      });
    }

    // Add Event
    function addEvent(eventData) {
      const $addButton = $('#addEventBtn');

      // Disable button immediately
      $addButton.prop('disabled', true).text(window.translations['Adding...'] || 'Adding...');

      // Clear previous errors for all fields
      $('#eventTitleError').hide().text('');
      $('#eventTitle').removeClass('is-invalid');
      $('#eventLabelError').hide().text('');
      $('#eventLabel').removeClass('is-invalid');
      $('#eventStartDateError').hide().text('');
      $('#eventStartDate').removeClass('is-invalid');
      $('#eventEndDateError').hide().text('');
      $('#eventEndDate').removeClass('is-invalid');
      $('#eventDescriptionError').hide().text('');
      $('#eventDescription').removeClass('is-invalid');

      $.ajax({
        url: '/school/event-calender',
        method: 'POST',
        data: eventData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          toastr.options.progressBar = true;

          if (response.success) {
            toastr.success(window.translations['Event created successfully!'] || 'Event created successfully!');
            calendar.refetchEvents();
            bsAddEventSidebar.hide();
            resetValues();
          } else {
            toastr.error(window.translations['Failed to create event'] || 'Failed to create event');
          }
          // Re-enable button
          $addButton.prop('disabled', false).text(window.translations['Add'] || 'Add');
        },
        error: function (error) {
          // Clear previous errors for all fields
          $('#eventTitleError').hide().text('');
          $('#eventTitle').removeClass('is-invalid');
          $('#eventLabelError').hide().text('');
          $('#eventLabel').removeClass('is-invalid');
          $('#eventStartDateError').hide().text('');
          $('#eventStartDate').removeClass('is-invalid');
          $('#eventEndDateError').hide().text('');
          $('#eventEndDate').removeClass('is-invalid');
          $('#eventDescriptionError').hide().text('');
          $('#eventDescription').removeClass('is-invalid');
          if (error.status === 422 && error.responseJSON && error.responseJSON.errors) {
            const errors = error.responseJSON.errors;
            if (errors.title) {
              $('#eventTitleError').show().text(errors.title[0]);
              $('#eventTitle').addClass('is-invalid');
            }
            if (errors.type) {
              $('#eventLabelError').show().text(errors.type[0]);
              $('#eventLabel').addClass('is-invalid');
            }
            if (errors.start_date) {
              $('#eventStartDateError').show().text(errors.start_date[0]);
              $('#eventStartDate').addClass('is-invalid');
            }
            if (errors.end_date) {
              $('#eventEndDateError').show().text(errors.end_date[0]);
              $('#eventEndDate').addClass('is-invalid');
            }
            if (errors.description) {
              $('#eventDescriptionError').show().text(errors.description[0]);
              $('#eventDescription').addClass('is-invalid');
            }
          } else {
            toastr.error('Failed to create event');
          }
          // Re-enable button even on failure
          $addButton.prop('disabled', false).text(window.translations['Add'] || 'Add');
        }
      });
    }

    // Update Event
    function updateEvent(eventData) {
      const $updateButton = $('#addEventBtn'); // Assuming you're using the same button for Add & Update

      // Clear previous errors for all fields
      $('#eventTitleError').hide().text('');
      $('#eventTitle').removeClass('is-invalid');
      $('#eventLabelError').hide().text('');
      $('#eventLabel').removeClass('is-invalid');
      $('#eventStartDateError').hide().text('');
      $('#eventStartDate').removeClass('is-invalid');
      $('#eventEndDateError').hide().text('');
      $('#eventEndDate').removeClass('is-invalid');
      $('#eventDescriptionError').hide().text('');
      $('#eventDescription').removeClass('is-invalid');

      // Disable the button to prevent multiple clicks
      $updateButton.prop('disabled', true).text(window.translations['Updating...'] || 'Updating...');

      $.ajax({
        url: `/school/event-calender/${eventToUpdate.id}`,
        method: 'PUT',
        data: eventData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          toastr.options.progressBar = true;

          if (response.success) {
            toastr.success(response.message || 'Event updated successfully!');
            calendar.refetchEvents();
            bsAddEventSidebar.hide();
            resetValues();
          } else {
            toastr.error(response.message || 'Failed to update event');
          }

          // Re-enable the button
          $updateButton.prop('disabled', false).text('Add'); // You may set text based on context
        },
        error: function (error) {
          // Clear previous errors for all fields
          $('#eventTitleError').hide().text('');
          $('#eventTitle').removeClass('is-invalid');
          $('#eventLabelError').hide().text('');
          $('#eventLabel').removeClass('is-invalid');
          $('#eventStartDateError').hide().text('');
          $('#eventStartDate').removeClass('is-invalid');
          $('#eventEndDateError').hide().text('');
          $('#eventEndDate').removeClass('is-invalid');
          $('#eventDescriptionError').hide().text('');
          $('#eventDescription').removeClass('is-invalid');
          if (error.status === 422 && error.responseJSON && error.responseJSON.errors) {
            const errors = error.responseJSON.errors;
            if (errors.title) {
              $('#eventTitleError').show().text(errors.title[0]);
              $('#eventTitle').addClass('is-invalid');
            }
            if (errors.type) {
              $('#eventLabelError').show().text(errors.type[0]);
              $('#eventLabel').addClass('is-invalid');
            }
            if (errors.start_date) {
              $('#eventStartDateError').show().text(errors.start_date[0]);
              $('#eventStartDate').addClass('is-invalid');
            }
            if (errors.end_date) {
              $('#eventEndDateError').show().text(errors.end_date[0]);
              $('#eventEndDate').addClass('is-invalid');
            }
            if (errors.description) {
              $('#eventDescriptionError').show().text(errors.description[0]);
              $('#eventDescription').addClass('is-invalid');
            }
          } else {
            toastr.error('Failed to update event');
          }

          // Re-enable the button
          $updateButton.prop('disabled', false).text('Update');
        }
      });
    }

    // Remove Event
    function removeEvent(eventId) {
      $.ajax({
        url: `/school/event-calender/${eventId}`,
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          toastr.options.progressBar = true;
          if (response.success) {
            toastr.success(response.message);
            calendar.refetchEvents();
            bsAddEventSidebar.hide();
            resetValues();
          } else {
            toastr.error(response.message || 'Failed to delete event');
          }
        },
        error: function (error) {
          toastr.error('Failed to delete event');
          console.error('Failed to delete event:', error);
        }
      });
    }

    // (Update Event In Calendar (UI Only)
    // ------------------------------------------------
    const updateEventInCalendar = (updatedEventData, propsToUpdate, extendedPropsToUpdate) => {
      const existingEvent = calendar.getEventById(updatedEventData.id);

      // --- Set event properties except date related ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setProp
      // dateRelatedProps => ['start', 'end', 'allDay']
      // eslint-disable-next-line no-plusplus
      for (var index = 0; index < propsToUpdate.length; index++) {
        var propName = propsToUpdate[index];
        existingEvent.setProp(propName, updatedEventData[propName]);
      }

      // --- Set date related props ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setDates
      existingEvent.setDates(updatedEventData.start, updatedEventData.end, {
        allDay: updatedEventData.allDay
      });

      // --- Set event's extendedProps ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setExtendedProp
      // eslint-disable-next-line no-plusplus
      for (var index = 0; index < extendedPropsToUpdate.length; index++) {
        var propName = extendedPropsToUpdate[index];
        existingEvent.setExtendedProp(propName, updatedEventData.extendedProps[propName]);
      }
    };

    // Remove Event In Calendar (UI Only)
    // ------------------------------------------------
    function removeEventInCalendar(eventId) {
      calendar.getEventById(eventId).remove();
    }

    // Add new event
    // ------------------------------------------------
    btnSubmit.addEventListener('click', e => {
      if (btnSubmit.classList.contains('btn-add-event')) {
        // Remove isFormValid check for now to allow form submission
        let newEvent = {
          title: eventTitle.value,
          start: eventStartDate.value,
          end: eventEndDate.value,
          startStr: eventStartDate.value,
          endStr: eventEndDate.value,
          display: 'block',
          extendedProps: {
            location: eventLocation.value,
            guests: eventGuests.val(),
            calendar: eventLabel.val(),
            description: eventDescription.value
          }
        };
        if (eventUrl.value) {
          newEvent.url = eventUrl.value;
        }
        if (allDaySwitch.checked) {
          newEvent.allDay = true;
        }
        addEvent(newEvent);
        bsAddEventSidebar.hide();
      } else {
        // Update event
        // ------------------------------------------------
        // Remove isFormValid check for now to allow form submission
        let eventData = {
          id: eventToUpdate.id,
          title: eventTitle.value,
          start: eventStartDate.value,
          end: eventEndDate.value,
          url: eventUrl.value,
          extendedProps: {
            location: eventLocation.value,
            guests: eventGuests.val(),
            calendar: eventLabel.val(),
            description: eventDescription.value
          },
          display: 'block',
          allDay: allDaySwitch.checked ? true : false
        };

        updateEvent(eventData);
        bsAddEventSidebar.hide();
      }
    });

    // Custom modal delete logic
    let eventIdToDelete = null;
    btnDeleteEvent.addEventListener('click', function (e) {
      e.preventDefault();
      eventIdToDelete = eventToUpdate.id;

      // Use the deleteEvent function from the blade file
      const deleteUrl = `/school/event-calender/${eventIdToDelete}`;

      // Check if deleteEvent function exists
      if (typeof window.deleteEvent === 'function') {
        window.deleteEvent(deleteUrl);
      } else {
        console.error('deleteEvent function is not defined');
        // Fallback to the original removeEvent function
        removeEvent(eventIdToDelete);
      }
    });

    // Reset event form inputs values
    // ------------------------------------------------
    function resetValues(disableDateFields = false) {
      // Reset basic form inputs
      if (eventEndDate) eventEndDate.value = '';
      if (eventUrl) eventUrl.value = '';
      if (eventStartDate) eventStartDate.value = '';
      if (eventTitle) eventTitle.value = '';
      if (eventLocation) eventLocation.value = '';
      if (eventDescription) eventDescription.value = '';

      // Reset all day switch
      if (allDaySwitch) {
        allDaySwitch.checked = false;
      }

      // Reset select2 dropdowns
      if (eventGuests && eventGuests.length) {
        eventGuests.val('').trigger('change');
      }

      if (eventLabel && eventLabel.length) {
        eventLabel.val('').trigger('change');
      }

      // Reset flatpickr instances
      if (start) {
        start.clear();
      }

      if (end) {
        end.clear();
      }

      // Reset eventToUpdate to null for new events
      eventToUpdate = null;

      // Reset form validation state
      isFormValid = false;

      // Reset button states
      if (btnSubmit) {
        btnSubmit.innerHTML = window.translations['Add'] || 'Add';
        btnSubmit.classList.remove('btn-update-event');
        btnSubmit.classList.add('btn-add-event');
        btnSubmit.style.display = '';
        btnSubmit.removeAttribute('disabled'); // Always re-enable update button
      }

      if (btnDeleteEvent) {
        btnDeleteEvent.classList.add('d-none');
        btnDeleteEvent.style.display = '';
      }

      // Re-enable all fields
      if (eventStartDate) {
        if (disableDateFields) {
          eventStartDate.setAttribute('disabled', 'disabled');
        } else {
          eventStartDate.removeAttribute('disabled');
        }
      }
      if (eventEndDate) {
        if (disableDateFields) {
          eventEndDate.setAttribute('disabled', 'disabled');
        } else {
          eventEndDate.removeAttribute('disabled');
        }
      }
      if (eventTitle) eventTitle.removeAttribute('disabled');
      if (eventLabel && eventLabel.length) eventLabel.prop('disabled', false);
      if (eventDescription) eventDescription.removeAttribute('disabled');

      // Reset offcanvas title
      if (offcanvasTitle) {
        offcanvasTitle.innerHTML = window.translations['Add Event'] || 'Add Event';
      }

      if (typeof end !== 'undefined' && end) {
        end.set('minDate', null); // Reset end date minDate
      }

      // Clear validation errors for all fields
      if ($('#eventTitleError').length) {
        $('#eventTitleError').hide().text('');
        $('#eventTitle').removeClass('is-invalid');
      }
      if ($('#eventLabelError').length) {
        $('#eventLabelError').hide().text('');
        $('#eventLabel').removeClass('is-invalid');
      }
      if ($('#eventStartDateError').length) {
        $('#eventStartDateError').hide().text('');
        $('#eventStartDate').removeClass('is-invalid');
      }
      if ($('#eventEndDateError').length) {
        $('#eventEndDateError').hide().text('');
        $('#eventEndDate').removeClass('is-invalid');
      }
      if ($('#eventDescriptionError').length) {
        $('#eventDescriptionError').hide().text('');
        $('#eventDescription').removeClass('is-invalid');
      }
    }

    // When modal hides reset input values
    addEventSidebar.addEventListener('hidden.bs.offcanvas', function () {
      resetValues();
    });

    // When modal shows for new events, ensure reset
    addEventSidebar.addEventListener('show.bs.offcanvas', function () {
      // Only reset if this is for adding a new event (not updating)
      if (!eventToUpdate) {
        // If today is past, disable date fields
        const now = new Date();
        now.setHours(0, 0, 0, 0);
        if (now > new Date()) {
          resetValues(true);
        } else {
          resetValues();
        }
      }
    });

    // Calender filter functionality
    // ------------------------------------------------
    if (selectAll) {
      selectAll.addEventListener('click', e => {
        if (e.currentTarget.checked) {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = 1));
        } else {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = 0));
        }
        calendar.refetchEvents();
      });
    }

    if (filterInput) {
      filterInput.forEach(item => {
        item.addEventListener('click', () => {
          document.querySelectorAll('.input-filter:checked').length < document.querySelectorAll('.input-filter').length
            ? (selectAll.checked = false)
            : (selectAll.checked = true);
          calendar.refetchEvents();
        });
      });
    }

    // Form submission handler
    $('#eventForm').on('submit', function (e) {
      e.preventDefault();

      const formData = {
        title: eventTitle.value,
        type: eventLabel.val(),
        start_date: eventStartDate.value,
        end_date: eventEndDate.value,
        description: eventDescription ? eventDescription.value.trim() : '' // Check if element exists
      };

      if (eventToUpdate) {
        updateEvent(formData);
      } else {
        addEvent(formData);
      }
    });
  })();
});
