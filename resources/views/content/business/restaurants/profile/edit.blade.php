@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', isset($flag) ? __('Update Profile') : __('Restaurant Profile Setup'))

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
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
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
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
    const messageEl = document.getElementById('updateProfileMessage');

    if (messageEl) {
      const message = messageEl.dataset.message;
      const type = messageEl.dataset.type || 'info';

      // Toggle between SweetAlert or Toastr
      if (['success', 'error', 'warning', 'info'].includes(type)) {
        let config = {
          icon: type,
          title: '',
          text: message,
          customClass: {
            confirmButton: 'btn waves-effect waves-light'
          },
          buttonsStyling: false
        };

        switch (type) {
          case 'success':
            config.title = 'Success!';
            config.customClass.confirmButton += ' btn-success';
            break;

          case 'error':
            config.title = 'Error!';
            config.customClass.confirmButton += ' btn-danger';
            break;

          case 'warning':
            config.title = 'Warning!';
            config.customClass.confirmButton += ' btn-warning';
            break;

          case 'info':
            config.title = 'Information';
            config.customClass.confirmButton += ' btn-info';
            break;
        }

        Swal.fire(config);
      }

    }
  });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function (e){
        const formAuthentication = document.querySelector('#formAuthentication');
        if (formAuthentication) {
            const fv = FormValidation.formValidation(formAuthentication, {
            fields: {
                'restaurant_name': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter the restaurant name'
                        },
                    }
                },
                'restaurant_categories[]': {
                    validators: {
                        notEmpty: {
                            message: 'Please select a restaurant type'
                        },
                    }
                },
                'cuisine_type[]': {
                    validators: {
                        notEmpty: {
                            message: 'Please select a cuisine type'
                        },
                    }
                },
                'address': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter address'
                        },
                    }
                },
                'city': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter city'
                        },
                    }
                },
                'pincode': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter pincode'
                        },
                    }
                },
                'mobile_number': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter mobile number'
                        },
                    }
                },
                'country': {
                    validators: {
                        notEmpty: {
                            message: 'Please enter country'
                        },
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.mb-3'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
            });
        }

      function parseTime(timeString){
        const [hour, minute] = timeString.split(':');
        return parseInt(hour) * 60 + parseInt(minute);
      }

      function hasValidationErrors() {
        return document.querySelectorAll('.is-invalid').length > 0;
      }

      for(let i=0; i<=6; i++){
          const startInput = document.querySelector(`[name="timings[${i}][start_time]"]`);
          const endInput = document.querySelector(`[name="timings[${i}][end_time]"]`);
          const isClosedInput = document.querySelector(`[name="timings[${i}][is_closed]"]`);
          const startErrorContainer = document.querySelector(`[data-error="timings[${i}][start_time]"]`);
          const endErrorContainer = document.querySelector(`[data-error="timings[${i}][end_time]"]`);
          const closedErrorContainer = document.querySelector(`[data-error="timings[${i}][is_closed]"]`);
          const submit = document.querySelector('button[type="submit"]');

          // Initialize state on page load
          if (isClosedInput && isClosedInput.checked) {
            startInput.disabled = true;
            endInput.disabled = true;
            startInput.style.opacity = '0.5';
            endInput.style.opacity = '0.5';
          }

          // Function to validate time inputs
          function validateTimeInputs() {
            const start = startInput?.value;
            const end = endInput?.value;
            const isClosed = isClosedInput?.checked;

            // Clear previous errors
            startInput?.classList.remove('is-invalid');
            endInput?.classList.remove('is-invalid');
            isClosedInput?.classList.remove('is-invalid');
            startErrorContainer.textContent = '';
            endErrorContainer.textContent = '';
            closedErrorContainer.textContent = '';

            // If is closed is checked, time inputs should be empty
            if (isClosed) {
              if (start) {
                startErrorContainer.textContent = 'Start time is not required when day is closed.';
                startInput.classList.add('is-invalid');
                return false;
              }
              if (end) {
                endErrorContainer.textContent = 'End time is not required when day is closed.';
                endInput.classList.add('is-invalid');
                return false;
              }
              return true;
            }

            // If is closed is not checked, both start and end times are required
            if (!start && !end) {
              startErrorContainer.textContent = 'Please enter start time or mark day as closed.';
              startInput.classList.add('is-invalid');
              return false;
            }

            if (start && !end) {
              endErrorContainer.textContent = 'Please enter end time.';
              endInput.classList.add('is-invalid');
              return false;
            }

            if (!start && end) {
              startErrorContainer.textContent = 'Please enter start time first.';
              startInput.classList.add('is-invalid');
              return false;
            }

            // Validate time logic
            if (start && end) {
              if (parseTime(end) <= parseTime(start)) {
                endErrorContainer.textContent = 'End time must be greater than start time.';
                endInput.classList.add('is-invalid');
                return false;
              }
            }

            return true;
          }

          startInput.addEventListener('input', function(e) {
            // Clear is closed when time inputs are filled
            if (e.target.value && isClosedInput.checked) {
              isClosedInput.checked = false;
              startInput.disabled = false;
              endInput.disabled = false;
              startInput.style.opacity = '1';
              endInput.style.opacity = '1';
            }
            validateTimeInputs();
            submit.disabled = hasValidationErrors();
          });

          endInput.addEventListener('input', function(e) {
            // Clear is closed when time inputs are filled
            if (e.target.value && isClosedInput.checked) {
              isClosedInput.checked = false;
              startInput.disabled = false;
              endInput.disabled = false;
              startInput.style.opacity = '1';
              endInput.style.opacity = '1';
            }
            validateTimeInputs();
            submit.disabled = hasValidationErrors();
          });

          isClosedInput.addEventListener('change', function(e) {
            // If is closed is checked, clear time inputs and disable them
            if (e.target.checked) {
              startInput.value = '';
              endInput.value = '';
              startInput.disabled = true;
              endInput.disabled = true;
              startInput.style.opacity = '0.5';
              endInput.style.opacity = '0.5';
            } else {
              startInput.disabled = false;
              endInput.disabled = false;
              startInput.style.opacity = '1';
              endInput.style.opacity = '1';
            }
            validateTimeInputs();
            submit.disabled = hasValidationErrors();
          });

      }

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded',function(e){
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
      $profile_setup = null;
      if (session()->has('restaurant_profile')) {
        $profile_setup = session('restaurant_profile');
      }
      $restaurant_data=$user?->restaurant;
      if($data==null){
        $data = new stdClass();
        $data->address=old('address');
        $data->url=old('address');
        $data->city=old('address');
        $data->mobile_number=old('address');
        $data->postalCode=old('address');
        $data->country=old('address');
      }
      // dd($restaurant_data->address);
      $restaurant_cuisine=$restaurant_data?->cuisines;
      $categories=$restaurant_data?->categories;
      $timings=$restaurant_data?->timings;
      // $sustainabilities = $restaurant_data?->sustainabilities;
      // $accessibilities = $restaurant_data?->accessibilities;
      // if($sustainabilities){
      //   $sustainabilities=collect(json_decode($sustainabilities));
      // }
      // if($accessibilities){
      //   $accessibilities=collect(json_decode($accessibilities));
      // }
      $timings_by_day = collect($timings)->keyBy('day');

      $isEditProfie = isset($flag) && $flag == 'edit';
      $profileHeading = $isEditProfie ? __('Edit Restaurant Profile') : __('Restaurant Profile Setup');
    @endphp

    @if (session()->exists('restaurant_profile'))
      @php
        $data = session()->get('restaurant_profile');
      @endphp
      <div class="d-none" id="updateProfileMessage"
        data-message="{{ $data['message'] }}"
        data-type="{{ $data['type'] }}">
      </div>
    @endif

<div class="row">
  <!-- Profile Setup School -->

  @php
   $isUpdate = isset($flag) && $flag === 'update';
   $cardTitle = $isUpdate ? __('Update Profile') : __('Restaurant Profile Setup');
  @endphp
  <div class="col-xxl">
    <div class="card mb-6">
      <h4 class="card-header">{{ __($profileHeading) }}</h4>
      <form id="formAuthentication" class="card-body" action="{{ route('restaurant.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h6>1. {{ __('Business Information') }}</h6>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="restaurant-name">{{ __('Restaurant Name') }}:</label>
          <div class="col-sm-9 mb-3">
            <input
                type="text"
                id="restaurant-name"
                name="restaurant_name"
                value="{{ $user->name }}"
                class="form-control"
                placeholder="{{ __('Enter Restaurant Name') }}"
            />
          </div>
          <small class="text-danger">
            {{ $errors->first('restaurant_name') }}
          </small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="restaurant-logo">{{ __('Restaurant Logo') }}:</label>
          <div class="col-sm-9 mb-3">
            <input type="file" id="restaurant-logo" name="restaurant_logo" class="form-control" accept="image/*" />
            <div class="form-text">{{ __('Upload Restaurant logo (JPG, PNG, max 2MB)') }}</div>
          </div>
          <small class="text-danger">{{ $errors->first('restaurant_logo') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="restaurant-categories">{{ __('Restaurant Type') }}:</label>
          <div class="col-sm-9 mb-3">
            <select id="restaurant-categories" class="select2 form-select" multiple name="restaurant_categories[]">
              <option value="">{{ __('Select Restaurant cuisine type(can be multiple)') }}</option>
              @if($restaurant_categories)
                @foreach($restaurant_categories as $category)
                    <option value="{{ $category->id }}" {{ $categories && $categories->contains('id', $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
              @endif
          </select>
          </div>
          <small class="text-danger">{{ $errors->first('restaurant_categories') }}</small>
        </div>

        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="select2Multiple">{{ __('Cuisine Type') }}:</label>
          <div class="col-sm-9 mb-3">
            <select id="select2Multiple" class="select2 form-select" multiple name="cuisine_type[]">
              <option value="">{{ __('Select Restaurant cuisine type(can be multiple)') }}</option>
              @if($cuisines)
                @foreach($cuisines as $cuisine)
                    {{-- <option value="{{ $cuisine->id }}" {{ $restaurant_cuisine && $restaurant_cuisine->contains('id', $cuisine->id) ? 'selected' : '' }}>
                        {{ $cuisine->name }}
                    </option> --}}
                    <option value="{{ $cuisine->id }}"
                        {{ collect(old('cuisine_type', $restaurant_cuisine?->pluck('id')->toArray() ?? []))->contains($cuisine->id) ? 'selected' : '' }}>
                        {{ $cuisine->name }}
                    </option>
                @endforeach
              @endif
          </select>
          </div>
          <small class="text-danger">{{ $errors->first('cuisine_type') }}</small>
        </div>

        <hr class="my-6 mx-n4" />

        <h6>2. {{ __('Address & Contact Details') }}</h6>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="restaurant-address">{{ __('Address') }}:</label>
          <div class="col-sm-9 mb-3">
              <textarea
                  id="restaurant-address"
                  name="address"
                  class="form-control"
                  rows="3"
                  placeholder="{{ __('Enter complete restaurant address') }}"
              >{{ old('address') ?? $restaurant_data?->address ?? $data?->address ?? '' }}</textarea>
          </div>
          <small class="text-danger">{{ $errors->first('address') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="restaurant-address-link">{{ __('Restaurant Address Url Google Map Link (Optional)') }}:</label>
          <div class="col-sm-9 mb-3">
            <input
                type="text"
                class="form-control" id="restaurant-address-link"
                name="address_link"
                value="{{ old('address_link', $restaurant_data?->address_link ?? $data?->url) ?? '' }}"
                placeholder="https://maps.app.goo.gl/DZdRDyaDrasf"
            >
          </div>
          <small class="text-danger">{{ $errors->first('address_link') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="city">{{ __('City') }}:</label>
          <div class="col-sm-9 mb-3">
            <input type="text" id="city" name="city" value="{{ old('city', $restaurant_data?->city ?? $data?->city) ??'' }}" class="form-control" placeholder="{{ __('Enter City') }}" />
          </div>
          <small class="text-danger">{{ $errors->first('city') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="pincode">{{ __('Postal Code') }}:</label>
          <div class="col-sm-9 mb-3">
            <input type="text" id="pincode" name="pincode" oninput="this.value = this.value.replace(/[^\d\s]/g, '')" value="{{ old('pincode', $restaurant_data?->pincode ?? $data?->postalCode) ??
                        '' }}" class="form-control" placeholder="{{ __('Enter Pincode') }}" />
          </div>
          <small class="text-danger">{{ $errors->first('pincode') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="phone">{{ __('Phone Number') }}:</label>
          <div class="col-sm-9 mb-3">
            <input type="text" id="phone" name="mobile_number" oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')"
                  value="{{ old('mobile_number', $restaurant_data?->mobile_number ?? $data?->mobileNumber ?? '')  ?? '' }}" class="form-control" placeholder="{{ __('Enter Phone Number') }}" />
          <span class="text-info text-small">*Note: The mobile number must be the same as your WhatsApp number and must include the country code first (e.g., 420123456789).</span>
          </div>
          <small class="text-danger">{{ $errors->first('mobile_number') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="country">{{ __('Country') }}:</label>
          <div class="col-sm-9 mb-3">
            <input type="text" id="country" name="country" value="{{ old('country', $restaurant_data?->country ?? $data?->country) ??'' }}"
                    class="form-control" placeholder="{{ __('Enter Country Name') }}" />
          </div>
          <small class="text-danger">{{ $errors->first('country') }}</small>
        </div>
        {{-- <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-email">Email</label>
          <div class="col-sm-9 mb-3">
            <input type="email" id="school-email" name="user[email]" value="{{ $school->email }}" class="form-control" placeholder="school@example.com" />
          </div>
          <small class="text-danger">{{ $errors->first('user.email') }}</small>
        </div> --}}
        <hr class="my-6 mx-n4" />

        <div class="row mb-6">
          <div class="col-sm-3">
            <span>{{ __('Day') }}</span>
          </div>
          <div class="col-sm-3 justify-content-center">
            <span>{{ __('Open time') }}</span>
          </div>
          <div class="col-sm-3">
            <span>{{ __('closing time') }}</span>
          </div>
          <div class="col-sm-3">
            <span>{{ __('is closed') }}</span>
          </div>
        </div>

        @php
          $days_array=['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        @endphp
        @foreach($days_array as $key => $value)
          <div class="row mb-6">
            <label class="col-sm-3 col-form-label">{{ __(ucwords($value)) }}</label>
            <div class="col-sm-3 mb-3">
              <input type="hidden" name="timings[{{$key}}][day]" value="{{$value}}"/>
              <input
                  type="text"
                  class="form-control flatpickr-time1"
                  placeholder="HH:MM"
                  value="{{ !$timings_by_day->isEmpty() ? $timings_by_day[$value]->start_time : ''}}"
                  name="timings[{{$key}}][start_time]"
              />
              <small class="text-danger " data-error="timings[{{$key}}][start_time]">{{ $errors->first('timings.'.$key.'.start_time') }}</small>
            </div>
            <div class="col-sm-3 mb-3">
              <input
                  type="text"
                  class="form-control flatpickr-time1"
                  placeholder="HH:MM"
                  value="{{!$timings_by_day->isEmpty() ? $timings_by_day[$value]->end_time : ''}}"
                  name="timings[{{$key}}][end_time]"
              />
              <small class="text-danger" data-error="timings[{{$key}}][end_time]">{{ $errors->first('timings.'.$key.'.end_time') }}</small>
            </div>
            <div class="col-sm-3">
              <div class="d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" value="1" id="{{$key}}" name="timings[{{$key}}][is_closed]" {{ !$timings_by_day->isEmpty() && $timings_by_day[$value]->is_closed ? 'checked' : '' }}/>
                <label class="form-check-label" for="{{$key}}">{{ __('is closed') }}</label>
              </div>
              <small class="text-danger" data-error="timings[{{$key}}][is_closed]">{{ $errors->first('timings.'.$key.'.is_closed') }}</small>
            </div>
          </div>
        @endforeach

        <hr class="my-6 mx-n4" />

        {{-- @php
          $restaurant_sustainabilities = config('constant.restaurant_sustainabilities');
          $basic_accessibilities = config('constant.restaurant_accessibilities_basic');
          $senior_accessibilities = config('constant.restaurant_accessibilities_senior_friendly');
          $student_accessibilities = config('constant.restaurant_accessibilities_student_friendly');
          $child_accessibilities = config('constant.restaurant_accessibilities_child_friendly');
        @endphp

        <h6>4. {{ __('Sustainability') }}</h6>

        <div class="row-mb-6 ms-6">
          @foreach ($restaurant_sustainabilities as $key=>$value)
          <div class="col-sm-6 mb-6">
            <div class="d-flex align-items-center gap-2">
              <input
                class="form-check-input"
                type="checkbox"
                value="{{$key}}"
                id="{{$key}}"
                name="sustainabilities[]"
                {{ $sustainabilities && $sustainabilities->contains($key) ? 'checked' : '' }}
              />
              <label class="form-check-label" for="{{$key}}">{{ __($value) }}</label>
            </div>

          </div>
          @endforeach
          <small class="text-danger">{{ $errors->first('sustainabilities') }}</small>
        </div>

        <hr class="my-6 mx-n4" />

        <h6>5. {{ __('Accessibility and convenience (optional)') }}</h6>

        <div class="row ms-6">
          <div class="col-md-6">
            @foreach ($basic_accessibilities as $key=>$value)
            <div class="mb-3">
              <div class="d-flex align-items-center gap-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="{{$key}}"
                  id="{{$key}}"
                  name="accessibilities[]"
                  {{ $accessibilities && $accessibilities->contains($key) ? 'checked' : '' }}
                />
                <label class="form-check-label" for="{{$key}}">{{ __($value) }}</label>
              </div>
              <small class="text-danger">{{ $errors->first('accessibilities') }}</small>
            </div>
            @endforeach
            <div>
              <h5>{{ __('Senior friendly') }}</h5>
            </div>
            @foreach ($senior_accessibilities as $key=>$value)
            <div class="mb-3 ms-2">
              <div class="d-flex align-items-center gap-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="{{$key}}"
                  id="{{$key}}"
                  name="accessibilities[]"
                  {{ $accessibilities && $accessibilities->contains($key) ? 'checked' : '' }}
                />
                <label class="form-check-label" for="{{$key}}">{{ __($value) }}</label>
              </div>
              <small class="text-danger">{{ $errors->first('accessibilities') }}</small>
            </div>
            @endforeach
          </div>
          <div class="col-md-6">
            <div>
              <h5>{{ __('Student friendly') }}</h5>
            </div>
            @foreach ($student_accessibilities as $key=>$value)
            <div class="mb-3 ms-2">
              <div class="d-flex align-items-center gap-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="{{$key}}"
                  id="{{$key}}"
                  name="accessibilities[]"
                  {{ $accessibilities && $accessibilities->contains($key) ? 'checked' : '' }}
                />
                <label class="form-check-label" for="{{$key}}">{{ __($value) }}</label>
              </div>
              <small class="text-danger">{{ $errors->first('accessibilities') }}</small>
            </div>
            @endforeach

            <div>
              <h5>{{ __('Child friendly') }}</h5>
            </div>
            @foreach ($child_accessibilities as $key=>$value)
            <div class="mb-3 ms-2">
              <div class="d-flex align-items-center gap-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="{{$key}}"
                  id="{{$key}}"
                  name="accessibilities[]"
                  {{ $accessibilities && $accessibilities->contains($key) ? 'checked' : '' }}
                />
                <label class="form-check-label" for="{{$key}}">{{ __($value) }}</label>
              </div>
              <small class="text-danger">{{ $errors->first('accessibilities') }}</small>
            </div>
            @endforeach
          </div>
        </div> --}}

        <h6>4. {{ __('Sustainability') }}</h6>

        <div class="row mb-6 ms-6">
            @foreach ($sustainabilities as $sustainability)
                <div class="col-sm-6 mb-6">
                    <div class="d-flex align-items-center gap-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="{{ $sustainability->id }}"
                            id="sustainability-{{ $sustainability->id }}"
                            name="sustainabilities[]"
                            {{ optional($user->restaurant->sustainability)->contains($sustainability->id) ? 'checked' : '' }}
                        />
                        <label class="form-check-label" for="sustainability-{{ $sustainability->id }}">
                            {{ __($sustainability->value) }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-6 mx-n4" />

        <h6>5. {{ __('Accessibility and convenience (optional)') }}</h6>
        <div class="row ms-6">
            @foreach ($accessibilities as $category => $items)
                <div class="col-md-6 mb-4">
                    <h5>{{ __(ucfirst($category) . ' friendly') }}</h5>
                    @foreach ($items as $item)
                        <div class="mb-3 ms-2">
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="{{ $item->id }}"
                                    id="accessibility-{{ $item->id }}"
                                    name="accessibilities[]"
                                    {{ optional($user->restaurant->accessibility)->contains('id', $item->id) ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="accessibility-{{ $item->id }}">
                                    {{ __($item->value) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="pt-6">
          <div class="row justify-content-end">
            <div class="w-100 mb-3 d-flex align-items-center justify-content-center">
              <button type="submit" class="btn btn-primary me-4 btn-custom">{{ __('Update Profile') }}</button>
              <!-- <button type="reset" class="btn btn-outline-secondary">Reset Profile</button> -->
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
