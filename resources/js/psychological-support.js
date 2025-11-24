// --------------------------------------------------
// Form Repeater for Office Hours (Days + Start/End Time)
// --------------------------------------------------
$(function () {
  const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

  function initFlatpickrs() {
    $('.flatpickr-time').each(function () {
      const currentValue = $(this).val();

      // Destroy old flatpickr instance
      if (this._flatpickr) {
        this._flatpickr.destroy();
      }

      // Reinitialize with preserved value
      flatpickr(this, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        allowInput: true,
        defaultDate: currentValue || null,
        onOpen: function (selectedDates, dateStr, instance) {
          // Always enable input on open
          $(instance.input).prop('disabled', false);
        }
      });
    });
    // After initializing, apply logic to disable end_time if start_time is empty
    $('.office-hours-repeater-item').each(function () {
      const $row = $(this);
      const $start = $row.find('input[name$="[start_time]"]');
      const $end = $row.find('input[name$="[end_time]"]');
      if (!$start.val()) {
        $end.val('');
        $end.prop('disabled', true);
      } else {
        $end.prop('disabled', false);
      }
    });
  }

  function updateDayDropdowns() {
    const selectedDays = $('.day-select')
      .map(function () {
        return $(this).val();
      })
      .get()
      .filter(Boolean);

    $('.day-select').each(function () {
      const $select = $(this);
      const currentVal = $select.val();

      $select.empty().append('<option value="">Select Day</option>');

      allDays.forEach(function (day) {
        if (!selectedDays.includes(day) || day === currentVal) {
          const selected = day === currentVal ? 'selected' : '';
          $select.append(`<option value="${day}" ${selected}>${day}</option>`);
        }
      });
    });
  }

  function updateAddButtonState() {
    const maxItems = 7;
    const $addBtn = $('[data-repeater-create]');
    const itemCount = $('.office-hours-repeater-item').length;
    if (itemCount >= maxItems) {
      $addBtn.prop('disabled', true);
    } else {
      $addBtn.prop('disabled', false);
    }
  }

  const $formRepeater = $('.form-repeater');

  if ($formRepeater.length) {
    $formRepeater.repeater({
      initEmpty: false,
      defaultValues: {
        day: '',
        start_time: '',
        end_time: ''
      },
      show: function () {
        $(this).slideDown();
        initFlatpickrs();
        updateDayDropdowns();
        updateAddButtonState();
      },
      hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
        setTimeout(() => {
          updateDayDropdowns();
          updateAddButtonState();
        }, 300);
      }
    });

    initFlatpickrs();
    updateDayDropdowns();
    updateAddButtonState();

    $(document).on('change', '.day-select', function () {
      updateDayDropdowns();
    });

    addTimeValidation();
  }

  // Add this function to allow dynamic population from edit AJAX
  window.populateOfficeHoursRepeater = function (officeHours) {
    let repeaterList = $('[data-repeater-list="office_hours"]');
    let $template = $('[data-repeater-item]:first').clone(true, true);
    repeaterList.empty();

    if (Array.isArray(officeHours) && officeHours.length > 0) {
      officeHours.forEach(function (item, index) {
        let $item = $template.clone(true, true).show();

        // Update input name attributes dynamically
        $item.find('input[name$="[start_time]"]').attr('name', `office_hours[${index}][start_time]`);
        $item.find('input[name$="[end_time]"]').attr('name', `office_hours[${index}][end_time]`);

        // Set day dropdown options and value
        const $select = $item.find('select.day-select');
        $select.attr('name', `office_hours[${index}][day]`);
        $select.empty().append('<option value="">Select Day</option>');
        allDays.forEach(function (day) {
          $select.append(`<option value="${day}"${day === item.day ? ' selected' : ''}>${day}</option>`);
        });
        $select.val(item.day).trigger('change');

        // Normalize time format
        let startTime = item.start_time;
        let endTime = item.end_time;

        if (startTime && !/^\d{2}:\d{2}$/.test(startTime)) {
          startTime = moment(startTime, ['h:mm A', 'HH:mm']).format('HH:mm');
        }
        if (endTime && !/^\d{2}:\d{2}$/.test(endTime)) {
          endTime = moment(endTime, ['h:mm A', 'HH:mm']).format('HH:mm');
        }

        // Initialize Flatpickr with default time
        $item.find(`input[name="office_hours[${index}][start_time]"]`).each(function () {
          flatpickr(this, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            allowInput: true,
            defaultDate: startTime || null
          });
        });

        $item.find(`input[name="office_hours[${index}][end_time]"]`).each(function () {
          flatpickr(this, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            allowInput: true,
            defaultDate: endTime || null
          });
        });
        repeaterList.append($item);
      });
    } else {
      repeaterList.append($template);
    }

    // Reinitialize any other hooks
    if (typeof updateDayDropdowns === 'function') updateDayDropdowns();
    if (typeof updateAddButtonState === 'function') updateAddButtonState();

    addTimeValidation();
  };

  // Ensure repeater is re-populated every time the edit modal is shown
  $(document).on('shown.bs.offcanvas', '#offcanvasAddUser', function () {
    if (window.currentOfficeHoursData) {
      window.populateOfficeHoursRepeater(window.currentOfficeHoursData);
    }
    // ✅ Fix: Always re-init Flatpickr when modal is opened
    setTimeout(() => {
      initFlatpickrs();
    }, 100); // Slight delay ensures modal is fully rendered
  });

  // Helper to fully reset the office hours repeater for Add
  window.resetOfficeHoursRepeater = function () {
    let repeaterList = $('[data-repeater-list="office_hours"]');
    let $template = $('[data-repeater-item]:first').clone(true, true);
    repeaterList.empty();
    repeaterList.append($template);
    // For the new row, reset all fields and repopulate all days
    let $select = repeaterList.find('select.day-select');
    $select.empty().append('<option value="">Select Day</option>');
    allDays.forEach(function (day) {
      $select.append(`<option value="${day}">${day}</option>`);
    });
    $select.val('');
    repeaterList.find('input[name$="[start_time]"]').val('');
    repeaterList.find('input[name$="[end_time]"]').val('');
    if (typeof updateDayDropdowns === 'function') updateDayDropdowns();
    if (typeof updateAddButtonState === 'function') updateAddButtonState();

    addTimeValidation();
  };

  function addTimeValidation() {
    // When start time changes, clear and enable end time, and set minTime
    $(document).on('change', 'input[name$="[start_time]"]', function () {
      const $row = $(this).closest('.office-hours-repeater-item');
      const $start = $row.find('input[name$="[start_time]"]');
      const $end = $row.find('input[name$="[end_time]"]');
      const startTime = $start.val();
      if (startTime) {
        $end.prop('disabled', false);
        $end.val('');
        if ($end[0]._flatpickr) {
          $end[0]._flatpickr.set('minTime', startTime);
        }
      } else {
        $end.val('');
        $end.prop('disabled', true);
        if ($end[0]._flatpickr) {
          $end[0]._flatpickr.set('minTime', null);
        }
      }
    });
    // When end time changes, validate logic
    function showTimeError($end, message) {
      $end.addClass('is-invalid');
      // Find the index of the row
      const $row = $end.closest('.office-hours-repeater-item');
      const rowIndex = $row.index() + 1; // 1-based index
      $end.closest('.col-md-4').find('.time-error').text(`Row ${rowIndex}: ${message}`);
    }
    function clearTimeError($end) {
      $end.removeClass('is-invalid');
      $end.closest('.col-md-4').find('.time-error').text('');
    }
    $(document).on('change', 'input[name$="[end_time]"]', function () {
      const $row = $(this).closest('.office-hours-repeater-item');
      const $start = $row.find('input[name$="[start_time]"]');
      const $end = $row.find('input[name$="[end_time]"]');
      const startTime = $start.val();
      const endTime = $end.val();
      clearTimeError($end);
      if (startTime && endTime) {
        if (endTime <= startTime) {
          showTimeError($end, 'End time must be after start time.');
          $end.val('');
          if ($end[0]._flatpickr) $end[0]._flatpickr.clear();
        }
      }
    });
  }

  $(document).on('hidden.bs.offcanvas', '#offcanvasAddUser', function () {
    window.currentOfficeHoursData = null;
  });
});
