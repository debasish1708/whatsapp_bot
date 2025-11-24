@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', __('Update School Profile'))

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
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
@vite(['resources/assets/vendor/js/dropdown-hover.js'])
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-layouts.js'])
<script>
  document.addEventListener('DOMContentLoaded', function (e){
    const formAuthentication = document.querySelector('#formAuthentication');
    if (formAuthentication) {
      const fv = FormValidation.formValidation(formAuthentication, {
        fields: {
          'school-logo': {
            validators: {
              file: {
                extension: 'jpg,jpeg,png',
                type: 'image/jpeg,image/png',
                maxSize: 2097152, // 2MB in bytes
                message: 'Please upload a valid image file (JPG, PNG, max 2MB)'
              }
            }
          },
          'school-type': {
            validators: {
              notEmpty: {
                message: 'Please select at least one school type'
              }
            }
          },
          'school-address': {
            validators: {
              notEmpty: {
                message: 'Please enter the address'
              }
            }
          },
          'school-address-link': {
            validators: {
              uri: {
                message: 'Please enter a valid URL'
              }
            }
          },
          'school-city': {
            validators: {
              notEmpty: {
                message: 'Please enter the city'
              }
            }
          },
          'school-pincode': {
            validators: {
              notEmpty: {
                message: 'Please enter the postal code'
              },
              regexp: {
                regexp: /^[0-9]{4,10}$/,
                message: 'Please enter a valid postal code'
              }
            }
          },
          'school-phone': {
            validators: {
              notEmpty: {
                message: 'Please enter the phone number'
              },
              regexp: {
                regexp: /^[0-9+ ]{7,20}$/,
                message: 'Please enter a valid phone number'
              }
            }
          },
          'school-country': {
            validators: {
              notEmpty: {
                message: 'Please enter the country'
              }
            }
          },
          'school_services': {
            validators: {
              choice: {
                min: 1,
                message: 'Please select at least one service'
              }
            }
          }
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
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var amountSwitch = document.getElementById('amount-switch');
    var amountSection = document.getElementById('amount-section');
    var requirePayment = document.getElementById('requires_payment');
    var amountInput = document.getElementById('amount-input');

    if (amountSwitch) {
      amountSwitch.addEventListener('change', function() {
        if (this.checked) {
          amountSection.style.display = '';
          requirePayment.value = 'true';
          amountInput.setAttribute('required', 'required');
        } else {
          amountSection.style.display = 'none';
          requirePayment.value = 'false';
          amountInput.removeAttribute('required');
          amountInput.value = '';
        }
      });
    }
  });
</script>
@endsection

@section('content')

    @php
      // Check if school profile exists to determine if this is create or update
      $isUpdate = isset($flag) && $flag === 'update';
      $formAction = $isUpdate ? route('school.profile.update',$school) : route('school.profile.store');
      $formMethod = $isUpdate ? 'PUT' : 'POST';
      $buttonText = $isUpdate ? __('Update Profile') : __('Create Profile');
      $cardTitle = $isUpdate ? __('Update Profile') : __('Setup School Profile');
    @endphp

<div class="row">
  <!-- Profile Setup School -->
  <div class="col-xxl">
    <div class="card mb-6">
      <h5 class="card-header">{{ $cardTitle }}</h5>
      <form id="formAuthentication" class="card-body" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isUpdate)
          @method('PUT')
        @endif

        <h6>{{ __('Basic School Information') }}</h6>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-name">{{ __('School Name') }}</label>
          <div class="col-sm-9">
            <input type="text" id="school-name" name="user[name]" value="{{ $school->name ?? $user->name ?? 'N/A' }}" class="form-control" placeholder="{{ __('Enter School Name') }}" readonly />
          </div>
          <small class="text-danger">{{ $errors->first('user.name') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-logo">{{ __('School Logo') }}</label>
          <div class="col-sm-9">
            <input type="file" id="school-logo" name="school[logo]" class="form-control" accept="image/*" />
            <div class="form-text">{{ __('Upload school logo (JPG, PNG, max 2MB)') }}</div>
            @if($isUpdate)
              <div class="mt-2">
                {{-- <img src="{{ $school->logo }}" alt="School Logo" class="img-fluid" style="max-width: 150px;"> --}}
              </div>
            @endif
          </div>
          <small class="text-danger">{{ $errors->first('school.logo') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-type">{{ __('School Type') }}</label>
          <div class="col-sm-9">
            <select class="form-select border-2 select2"  id="school-type" multiple name="school[categories][]" data-allow-clear="true" data-placeholder="{{ __('Select School Type') }}">
              <option value="">{{ __('Select School Category') }}</option>

              @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ ($user && $schoolDetail->categories->contains($category)) ? 'selected' : '' }}>
                      {{ $category->name }}
                  </option>
              @endforeach
          </select>
          </div>
          <small class="text-danger">{{ $errors->first('school.categories') }}</small>
        </div>

        <hr class="my-6 mx-n4" />

        <h6>{{ __('Address & Contact Details') }}</h6>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-address">{{ __('Address') }}</label>
          <div class="col-sm-9">
            <textarea id="school-address" name="school[address]" class="form-control" rows="3" placeholder="{{ __('Enter complete school address') }}">{{ old('school.address') ?? $school->address ?? ($placeApiData->address ?? '') }}</textarea>
          </div>
          <small class="text-danger">{{ $errors->first('school.address') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-address">{{ __('School Address Url Google Map Link (Optional)') }}</label>
          <div class="col-sm-9">
           <input type="text" class="form-control" id="school-address-link" name="school[address_link]" value="{{ old('school.address_link') ?? $school->address_link ?? ($placeApiData->url ?? '') }}"
                placeholder="https://maps.app.goo.gl/DZdRDyaDrasf">
          </div>
          <small class="text-danger">{{ $errors->first('school.address_link') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-city">{{ __('City') }}</label>
          <div class="col-sm-9">
            <input type="text" id="school-city" name="school[city]" value="{{ old('school.city') ?? $school->city ?? ($placeApiData->city ?? '') }}" class="form-control" placeholder="{{ __('Enter City') }}" />
            <small class="text-danger validation-error" id="error-school-city" style="display:none"></small>
          </div>
          <small class="text-danger">{{ $errors->first('school.city') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-pincode">{{ __('Postal Code') }}</label>
          <div class="col-sm-9">
            <input type="digit" id="school-pincode" name="school[pincode]" value="{{ old('school.pincode') ?? $school->pincode ?? ($placeApiData->postalCode ?? '') }}"
              class="form-control" placeholder="{{ __('Enter Pincode') }}" oninput="this.value = this.value.replace(/[^\d\s]/g, '')"/>
          </div>
          <small class="text-danger">{{ $errors->first('school.pincode') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-phone">{{ __('Phone Number') }}</label>
          <div class="col-sm-9">
            @php
              $mobile = $school->mobile_number ?? ($placeApiData?->mobileNumber
                    ? str_replace(' ', '', $placeApiData?->mobileNumber)
                    : '');
            @endphp
            <input type="text" id="school-phone" name="school[mobile_number]" value="{{ old('school.mobile_number') ?? $mobile ?? '' }}"
              class="form-control" placeholder="{{ __('Enter Phone Number') }}" oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')" />
             <span class="text-info text-small">*Note: The mobile number must be the same as your WhatsApp number and must include the country code first (e.g., 420123456789).</span>
          </div>
          <small class="text-danger">{{ $errors->first('school.mobile_number') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-phone">{{ __('Country') }}</label>
          <div class="col-sm-9">
            <input type="text" id="school-country" name="school[country]" value="{{ old('school.country') ?? $school->country ?? ($placeApiData->country ?? '') }}" class="form-control" placeholder="{{ __('Enter Country Name') }}" />
            <small class="text-danger validation-error" id="error-school-country" style="display:none"></small>
          </div>
          <small class="text-danger">{{ $errors->first('school.country') }}</small>
        </div>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label" for="school-email">{{ __('Email') }}</label>
          <div class="col-sm-9">
            <input type="email" id="school-email" name="user[email]" value="{{ old('user.email') ?? $user->email ?? $school->email ?? '' }}" class="form-control" readonly />
          </div>
          <small class="text-danger">{{ $errors->first('user.email') }}</small>
        </div>
        <hr class="my-6 mx-n4" />
        <!-- Switcher for Amount Section -->
        <!-- Toggle -->
        <div class="row mb-6 align-items-center">
          <label class="col-sm-3 col-form-label" for="amount-switch">{{ __('Enable Amount') }}</label>
          <div class="col-sm-9">
            <div class="form-check form-switch">
              <input class="form-check-input"
                    type="checkbox"
                    id="amount-switch"
                    {{ old('school.requires_payment', $schoolDetail->requires_payment ?? false) ? 'checked' : '' }}>

              <label class="form-check-label" for="amount-switch">{{ __('Enter Amount') }}</label>

              <!-- Hidden input to store boolean value -->
              <input type="hidden"
                    name="school[requires_payment]"
                    id="requires_payment"
                    value="{{ old('school.requires_payment', $schoolDetail->requires_payment ?? false) ? 'true' : 'false' }}">
            </div>
          </div>
          <small class="text-danger">{{ $errors->first('school.requires_payment') }}</small>
        </div>

          <!-- Amount input -->
          <div class="row mb-6"
              id="amount-section"
              style="{{ old('school.requires_payment', $schoolDetail->requires_payment ?? false) ? '' : 'display:none;' }}">
            <label class="col-sm-3 col-form-label" for="amount-input">{{ __('Amount') }}</label>
            <div class="col-sm-9">
              <input type="number"
                    class="form-control"
                    id="amount-input"
                    name="school[admission_fee]"
                    placeholder="{{ __('Enter Amount') }}"
                    min="0"
                    step="0.01"
                    value="{{ old('school.admission_fee', $schoolDetail->admission_fee ?? '') }}"
                    {{ old('school.requires_payment', $schoolDetail->requires_payment ?? false) ? 'required' : '' }}>
            </div>
            <small class="text-danger">{{ $errors->first('school.admission_fee') }}</small>
          </div>


        <hr class="my-6 mx-n4" />

        <h6>{{ __('Services & Programs') }}</h6>
        <div class="row mb-6">
          <label class="col-sm-3 col-form-label">{{ __('Services Offered') }}</label>
          <div class="col-sm-9">
            @php
              $services = old('school.services') ?? ($school->services ?? []);
              if (is_string($services)) {
                $services = json_decode($services, true) ?? [];
              }
            @endphp
            <!-- First Group: General Services -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-daycare" value="daycare" {{ in_array('daycare', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-daycare">{{ __('Day Care') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-afterschool" value="afterschool" {{ in_array('afterschool', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-afterschool">{{ __('After School Care') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-transport" value="transport" {{ in_array('transport', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-transport">{{ __('Transportation') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-meals" value="meals" {{ in_array('meals', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-meals">{{ __('Meals/Cafeteria') }}</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-library" value="library" {{ in_array('library', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-library">{{ __('Library') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-sports" value="sports" {{ in_array('sports', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-sports">{{ __('Sports Facilities') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-arts" value="arts" {{ in_array('arts', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-arts">{{ __('Arts & Music') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="service-counseling" value="counseling" {{ in_array('counseling', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="service-counseling">{{ __('Counseling') }}</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-stem" value="stem" {{ in_array('stem', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-stem">{{ __('STEM Programs') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-bilingual" value="bilingual" {{ in_array('bilingual', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-bilingual">{{ __('Bilingual Education') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-advanced" value="advanced" {{ in_array('advanced', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-advanced">{{ __('Advanced Placement') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-special" value="special" {{ in_array('special', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-special">{{ __('Special Education') }}</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-gifted" value="gifted" {{ in_array('gifted', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-gifted">{{ __('Gifted Programs') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-online" value="online" {{ in_array('online', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-online">{{ __('Online Learning') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-religious" value="religious" {{ in_array('religious', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-religious">{{ __('Religious Education') }}</label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="school[services][]" data-field="school_services" id="highlight-uniform" value="uniform" {{ in_array('uniform', $services) ? 'checked' : '' }}>
                  <label class="form-check-label" for="highlight-uniform">{{ __('Uniform Required') }}</label>
                </div>
              </div>
            </div>

          </div>
          <small class="text-danger">{{ $errors->first('school.services') }}</small>
        </div>

        <hr class="my-6 mx-n4" />

        <div class="pt-6">
          <div class="row justify-content-end">
            <div class="col-sm-9">
              <button type="submit" class="btn btn-primary me-3 data-submit btn-custom">{{ $buttonText }}</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
