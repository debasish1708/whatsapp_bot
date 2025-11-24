@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Admission Form - School')

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
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var flatpickrDate = document.querySelector('#flatpickr-date');
      if (flatpickrDate) {
        flatpickrDate.flatpickr({
          monthSelectorType: 'static',
          maxDate: 'today' // Initial minimum date
        });
      }

      @if (session('application_form_success'))
      Swal.fire({
        icon: 'success',
        title: 'Success!',
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
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
      <!-- Admission form -->
      <div class="row">
        <!-- FormValidation -->
        <div class="col-12">
          <div class="card">
            <h5 class="card-header">
              {{ $lang == 'english' ? 'Job Form' : 'Formulář práce' }}
            </h5>
            <div class="card-body pt-2">

              <div class="col-12">
                <h6>{{ $lang == 'english' ? 'Job Information' : 'Informace o práci' }}</h6>
                <hr class="mt-0" />
              </div>
                <ul class="list-unstyled my-3 py-1">
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Position' : 'Pozice' }}:</span><br>
                    <span>{{ $job_offer->position }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Location' : 'Místo' }}:</span><br>
                    <span>{{ $job_offer->location ?? 'N/A' }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Salary' : 'Plat' }}:</span><br>
                    <span>{{ $job_offer->salary ?? 'N/A' }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Expiry Date' : 'Datum ukončení' }}:</span><br>
                    <span>{{ $job_offer->expiry_date ?? 'N/A' }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Status' : 'Stav' }}:</span><br>
                    <span>{{ $job_offer->status ?? 'N/A' }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Job Description' : 'Popis práce' }}:</span><br>
                    <span>{{ $job_offer->description ?? 'N/A' }}</span>
                  </li>
                </ul>
              <form id="formValidationExamples" class="row g-6" action="{{ route('job-application.storeApplication') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Job Application Form -->
                <div class="col-12">
                  <h6>{{ $lang == 'english' ? 'Job Application Form' : 'Žádost o zaměstnání' }}</h6>
                  <hr class="mt-0" />
                </div>

                <input type="hidden" name="job_offer_id" value="{{ $job_offer->id }}" />

                <div class="col-md-6">
                  <label class="form-label" for="formValidationFirstName">{{ $lang == 'english' ? 'First Name' : 'Křestní jméno' }} *</label>
                  <input type="text" id="formValidationFirstName" class="form-control @error('first_name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'John' : 'Jan' }}" value="{{old('first_name')}}" name="first_name" required/>
                  @error('first_name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationLastName">{{ $lang == 'english' ? 'Last Name' : 'Příjmení' }} *</label>
                  <input type="text" id="formValidationLastName" class="form-control @error('last_name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'Doe' : 'Novák' }}" value="{{old('last_name')}}"  name="last_name" required/>
                  @error('last_name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationEmail">{{ $lang == 'english' ? 'Email' : 'E-mail' }} *</label>
                  <input class="form-control @error('email') is-invalid @enderror" type="email" id="formValidationEmail" name="email" value="{{old('email')}}"  placeholder="{{ $lang == 'english' ? 'john@gmail.com' : 'jan@example.com' }}" required/>
                  @error('email')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationMob">{{ $lang == 'english' ? 'Mobile Number' : 'Telefonní číslo' }} *</label>
                  <input class="form-control @error('mobile_number') is-invalid @enderror" type="text" id="formValidationMob" name="mobile_number" value="{{old('mobile_number')}}"  placeholder="{{ $lang == 'english' ? '420123456789' : '420123456789' }}" required/>
                  <span class="text-danger text-small">{{ $lang == 'english' ? '*Note: Use the same mobile number as your WhatsApp, including the country code (e.g., 420123456789).' : '*Poznámka: Použijte stejné číslo jako na WhatsApp včetně předvolby (např. 420123456789).' }}</span>
                  @error('mobile_number')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="flatpickr-date">{{ $lang == 'english' ? 'Date of Birth' : 'Datum narození' }} *</label>
                  <input type="text" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" placeholder="YYYY-MM-DD" id="flatpickr-date" value="{{old('date_of_birth')}}"  required />
                  @error('date_of_birth')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="form-check-label" class="form-check-label">{{ $lang == 'english' ? 'Gender' : 'Pohlaví' }} *</label>
                  <div class="col mt-2">
                      <div class="form-check form-check-inline">
                          <input name="gender" class="form-check-input" type="radio" value="male"
                              id="male" {{ old('gender')=='male'? 'checked' : '' }} checked required/>
                          <label class="form-check-label" for="male">{{ $lang == 'english' ? 'Male' : 'Muž' }}</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input name="gender" class="form-check-input" type="radio" value="female"
                              id="female" {{ old('gender')=='female'? 'checked' : '' }}/>
                          <label class="form-check-label" for="female">{{ $lang == 'english' ? 'Female' : 'Žena' }}</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input name="gender" class="form-check-input" type="radio" value="other"
                              id="other" {{ old('gender')=='other'? 'checked' : '' }}/>
                          <label class="form-check-label" for="other">{{ $lang == 'english' ? 'Other' : 'Jiné' }}</label>
                      </div>
                  </div>
                  @error('gender')
                      <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="formValidationCity">{{ $lang == 'english' ? 'City' : 'Město' }} *</label>
                  <input type="text" id="formValidationCity" class="form-control @error('city') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'Prague' : 'Praha' }}" value="{{old('city')}}"  name="city" required/>
                  @error('city')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationAddress">{{ $lang == 'english' ? 'Address' : 'Adresa' }} *</label>
                  <textarea class="form-control @error('address') is-invalid @enderror" id="formValidationAddress" name="address" rows="3" placeholder="{{ $lang == 'english' ? 'Enter your full address' : 'Zadejte svou úplnou adresu' }}" required>{{old('address')}}</textarea>
                  @error('address')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-12">
                  <label class="form-label" for="formValidationResume">{{ $lang == 'english' ? 'Resume/CV' : 'Životopis' }} *</label>
                  <input type="file" class="form-control @error('resume') is-invalid @enderror" id="formValidationResume" name="resume" accept=".pdf,.doc,.docx" required/>
                  <small class="text-muted">{{ $lang == 'english' ? 'Accepted formats: PDF, DOC, DOCX (Max size: 2MB)' : 'Přijímané formáty: PDF, DOC, DOCX (Max. velikost: 2 MB)' }}</small>
                  @error('resume')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input @error('formValidationCheckbox') is-invalid @enderror" id="formValidationCheckbox" name="formValidationCheckbox" required />
                    <label class="form-check-label" for="formValidationCheckbox">{{ $lang == 'english' ? 'I agree to the terms and conditions *' : 'Souhlasím s podmínkami a pravidly *' }}</label>
                  </div>
                  @error('formValidationCheckbox')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-12">
                  <button type="submit" name="submitButton" class="btn btn-primary me-3 data-submit btn-custom btn-lg px-5 py-3 rounded-pill shadow">
                    <i class="fas fa-paper-plane me-2"></i>{{ $lang == 'english' ? 'Submit Application' : 'Odeslat žádost' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- /FormValidation -->
      </div>
      <!-- /Admission form -->
</div>
</div>
@endsection
