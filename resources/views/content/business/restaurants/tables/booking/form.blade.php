@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.layoutMaster')

@section('title', $lang == 'english' ? 'Table Booking Form' : 'Formulář rezervace stolu')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@vite([
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
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
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
  'resources/assets/vendor/libs/pickr/pickr.js'
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
  'resources/assets/js/forms-typeahead.js',
])
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var flatpickrDate = document.querySelector('#flatpickr-date');
      if (flatpickrDate) {
        flatpickrDate.flatpickr({
          monthSelectorType: 'static',
          minDate: 'today' // Initial minimum date
        });
      }

      @if (session('application_form_success'))
      Swal.fire({
        icon: 'success',
        title: "{{ $lang == 'english' ? 'Success!' : 'Úspěch!' }}",
        text: "{{ session('application_form_success') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-success'
        },
        buttonsStyling: false
      })    .then((result) => {
        if (result.isConfirmed) {
          // Replace with your WhatsApp number
          setTimeout(() => {
            window.open('https://wa.me/{{config('constant.360dialog_whatsapp_number')}}', '_blank'); // ✅ Removed '+'
          }, 1000); // 2000ms = 2 seconds
        }
      });
    @endif
    });
  </script>
@vite([
  'resources/assets/js/pages-auth.js'
])
@vite(['resources/assets/js/form-validation.js'])
@vite(['resources/assets/js/forms-pickers.js'])
<script>
  document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#booking_start_time', {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i',
      time_24hr: true
    });
    flatpickr('#booking_end_time', {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i',
      time_24hr: true
    });
  });
</script>
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
      <!-- Admission form -->
      <div class="row">
        <!-- FormValidation -->
        <div class="col-12">
          <div class="card">
            <h5 class="card-header">{{ $lang == 'english' ? 'Table Booking' : 'Rezervace stolu' }}</h5>
            <div class="card-body pt-2">

              <div class="col-12">
                <h6>{{ $lang == 'english' ? 'Restaurant Info' : 'Informace o restauraci' }}</h6>
                <hr class="mt-0" />
              </div>
                <ul class="list-unstyled my-3 py-1">
                    <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Restaurant Name :' : 'Název restaurace :' }}</span><br>
                    <span>{{ $restaurant->user->name }}</span>
                    </li>
                    <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Mobile Number :' : 'Telefonní číslo :' }}</span><br>
                    <span>{{ $restaurant->mobile_number }}</span>
                    </li>
                    <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Address :' : 'Adresa :' }}</span><br>
                    <span>{{ $restaurant->address ?? 'N/A' }}</span>
                    </li>
                  {{-- <li class="d-flex align-items-center mb-4">
                    <i class="ti ti-world ti-lg"></i>
                    <span class="fw-medium mx-2">Booking Fee :</span>
                    <span>{{ $restaurant->booking_fee ?? 'N/A' }}</span>
                  </li> --}}
                </ul>
              <form id="formValidationExamples" class="row g-6" action="{{ route('restaurant.table.book') }}" method="POST">
                @csrf
                <!-- Account Details -->
                <div class="col-12">
                  <h6>{{ $lang == 'english' ? 'Booking Form' : 'Formulář rezervace' }}</h6>
                  <hr class="mt-0" />
                </div>

                <input type="hidden" name="restaurant_id" value="{{$restaurant->id}}" />
                <input type="hidden" name="user_id" value="{{$user->id}}" />
                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'Name' : 'Jméno' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'John' : 'Jan' }}" value="{{old('name')?? $user->name }}" name="name"  required/>
                  @error('name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationMob">{{ $lang == 'english' ? 'Mobile Number' : 'Telefonní číslo' }}</label>
                  <input class="form-control  @error('mobile_number') is-invalid @enderror" type="text" id="formValidationMob" name="mobile_number" value="{{old('mobile_number') ?? $user->mobile_number ?? ''}}"  readonly required/>
                  <span class="text-danger text-small"></span>
                  @error('mobile_number')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="table_id">{{ $lang == 'english' ? 'Select Table' : 'Vyberte stůl' }}</label>
                  <select id="table_id" name="table_id" class="select2 form-select @error('table_id') is-invalid @enderror" required>
                  <option value="">{{ $lang == 'english' ? '-- Select Table --' : '-- Vyberte stůl --' }}</option>
                  @foreach($tables as $table)
                    <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                    {{ $lang == 'english' ? 'Table no -' : 'Stůl č. -' }} {{ $table->number }} ({{ $lang == 'english' ? 'Seats' : 'Míst' }}: {{ $table->capacity }})
                    </option>
                  @endforeach
                  </select>
                  @error('table_id')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="flatpickr-date">{{ $lang == 'english' ? 'Booking Date' : 'Datum rezervace' }}</label>
                  <input type="text" id="flatpickr-date" class="form-control flatpickr @error('booking_date') is-invalid @enderror" name="booking_date" value="{{ old('booking_date') }}" placeholder="YYYY-MM-DD" required/>
                  @error('booking_date')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>


                <div class="col-md-6">
                  <label class="form-label" for="table_id">{{ $lang == 'english' ? 'Select hours' : 'Vyberte hodiny' }}</label>
                  <select id="time_slot" name="time_slot" class="select2 form-select @error('time_slot') is-invalid @enderror" required >
                    <option value="">{{ $lang == 'english' ? '-- Select Time Slot --' : '-- Vyberte časový slot --' }}</option>

                    @foreach($tableHours as $timeSlot)
                      <option value="{{ $timeSlot->id }}">
                        {{ $timeSlot->time_slot }}
                      </option>
                    @endforeach
                  </select>
                  @error('time_slot')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-12">
                  <button type="submit" name="submitButton" class="btn btn-primary me-3 data-submit btn-custom btn-lg px-4 py-2 rounded-pill shadow">{{ $lang == 'english' ? 'Submit' : 'Odeslat' }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- /FormValidation -->
      </div>
      <!-- /Admission form -->
</div>
@endsection
