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
          maxDate: 'today' // Initial minimum date
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
              {{ $lang == 'english' ? 'Admission' : 'Přijetí' }}
            </h5>
            <div class="card-body pt-2">

              <div class="col-12">
                <h6>{{ $lang == 'english' ? 'School info' : 'Informace o škole' }}</h6>
                <hr class="mt-0" />
              </div>
                <ul class="list-unstyled my-3 py-1">
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'School Name :' : 'Název školy :' }}</span><br>
                    <span>{{ $school->user->name }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Mobile Number :' : 'Telefonní číslo :' }}</span><br>
                    <span>{{ $school->mobile_number }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Address :' : 'Adresa :' }}</span><br>
                    <span>{{ $school->address ?? 'N/A' }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">{{ $lang == 'english' ? 'Admission Fee :' : 'Poplatek za přijetí :' }}</span><br>
                    <span>{{ $school->admission_fee ?? 'N/A' }}</span>
                  </li>
                </ul>
              <form id="formValidationExamples" class="row g-6" action="{{ route('school.admission.store') }}" method="POST">
                @csrf
                <!-- Account Details -->
                <div class="col-12">
                  <h6>{{ $lang == 'english' ? 'Admission Form' : 'Přihláška' }}</h6>
                  <hr class="mt-0" />
                </div>

                <input type="hidden" name="school_id" value="{{$school->id}}" />
                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'First Name' : 'Jméno' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('first_name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'John' : 'Jan' }}" value="{{old('first_name')}}" name="first_name" required/>
                  @error('first_name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'Last Name' : 'Příjmení' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('last_name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'Doe' : 'Novák' }}" value="{{old('last_name')}}"  name="last_name" required/>
                  @error('last_name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'Parent Name' : 'Jméno rodiče' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('parents_name') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'Doe' : 'Novák' }}" value="{{old('parents_name')}}"  name="parents_name" required/>
                  @error('parents_name')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationEmail">{{ $lang == 'english' ? 'Email' : 'E-mail' }}</label>
                  <input class="form-control  @error('email') is-invalid @enderror" type="email" id="formValidationEmail" name="email" value="{{old('email')}}"  placeholder="john@gmail.com" required/>
                  @error('email')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationMob">{{ $lang == 'english' ? 'Mobile Number' : 'Telefonní číslo' }}</label>
                  <input class="form-control  @error('mobile_number') is-invalid @enderror" type="text" id="formValidationMob" name="mobile_number" value="{{old('mobile_number')}}"  placeholder="420123456789" required/>
                  <span class="text-danger text-small">{{ $lang == 'english' ? '*Note: Use the same mobile number as your WhatsApp, including the country code (e.g., 420123456789).' : '*Poznámka: Použijte stejné telefonní číslo jako na WhatsAppu, včetně předvolby (např. 420123456789).' }}</span>
                  @error('mobile_number')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationMob">{{ $lang == 'english' ? 'Parent Mobile Number' : 'Telefon rodiče' }}</label>
                  <input class="form-control  @error('parent_mobile_number') is-invalid @enderror" type="text" id="formValidationMob" name="parent_mobile_number" value="{{old('parent_mobile_number')}}"  placeholder="420123456789" required/>
                  <span class="text-danger text-small">{{ $lang == 'english' ? '*Note: Use the same mobile number as your WhatsApp, including the country code (e.g., 420123456789).' : '*Poznámka: Použijte stejné telefonní číslo jako na WhatsAppu, včetně předvolby (např. 420123456789).' }}</span>
                  @error('mobile_number')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="flatpickr-date">{{ $lang == 'english' ? 'Date of Birth' : 'Datum narození' }}</label>
                  <input type="text" class="form-control  @error('date_of_birth') is-invalid @enderror" name="date_of_birth" placeholder="YYYY-MM-DD" id="flatpickr-date" value="{{old('date_of_birth')}}"  required />
                  @error('date_of_birth')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label for="form-check-label" class="form-check-label">{{ $lang == 'english' ? 'Gender' : 'Pohlaví' }}</label>
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
                              id="others" {{ old('gender')=='other'? 'checked' : '' }}/>
                          <label class="form-check-label" for="others">{{ $lang == 'english' ? 'Others' : 'Jiné' }}</label>
                      </div>
                  </div>
                  @error('gender')
                      <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'School Grades' : 'Školní ročník' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('grade') is-invalid @enderror" placeholder="{{ $lang == 'english' ? 'e.g., 5th Grade' : 'např. 5. ročník' }}" value="{{old('grade')}}"  name="grade" required/>
                  @error('grade')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                {{-- <div class="col-md-6" id="schoolGradesRow">
                  <label for="schoolGrades" class="form-label fw-semibold text-dark">
                    School Grades
                  </label>
                  <div class="w-100 mb-3">
                    <select id="selectSchoolGrades" class="select2 form-select" name="school_grades">
                       @php
                        $czechGrades = [
                            'Preschool (3 years)',
                            'Preschool (4 years)',
                            'Preschool (5 years - compulsory)',
                            '1st Grade',
                            '2nd Grade',
                            '3rd Grade',
                            '4th Grade',
                            '5th Grade',
                            '6th Grade',
                            '7th Grade',
                            '8th Grade',
                            '9th Grade',
                            '10th Grade',
                            '11th Grade',
                            '12th Grade',
                            '13th Grade',
                        ];
                       @endphp
                        @if($czechGrades)
                            @foreach($czechGrades as $grades)
                                <option value="{{ $grades }}"
                                    @if(is_array(old('school_grades')) && in_array($grades, old('school_grades')))
                                        selected
                                    @endif
                                >
                                    {{ $grades }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                  </div>
                  @error('school_grades')
                    <p class="text-danger">{{ $errors->first('school_grades') }}</p>
                  @enderror
                </div> --}}

                <div class="col-md-6">
                  <label class="form-label" for="formValidationName">{{ $lang == 'english' ? 'City' : 'Město' }}</label>
                  <input type="text" id="formValidationName" class="form-control  @error('city') is-invalid @enderror" placeholder="Prague" value="{{old('city')}}"  name="city" required/>
                  @error('city')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="formValidationAddress">{{ $lang == 'english' ? 'Address' : 'Adresa' }}</label>
                  <textarea class="form-control  @error('address') is-invalid @enderror" id="formValidationAddress" name="address" rows="3">{{old('address')}}</textarea>
                  @error('address')
                  <p class="text-danger">{{ $message }}</p>
                  @enderror
                </div>

                <div class="col-12">
                  <div class="form-check">
                  <input type="checkbox" class="form-check-input @error('formValidationCheckbox') is-invalid @enderror" id="formValidationCheckbox" name="formValidationCheckbox" required />
                  <label class="form-check-label" for="formValidationCheckbox">{{ $lang == 'english' ? 'Agree to our terms and conditions' : 'Souhlasím s podmínkami' }}</label>
                  @error('formValidationCheckbox')
                    <p class="text-danger">{{ $message }}</p>
                  @enderror
                  </div>
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
