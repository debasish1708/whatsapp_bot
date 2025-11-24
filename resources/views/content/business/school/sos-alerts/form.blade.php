@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Sos Aleart form - School')

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
            <h5 class="card-header">Sos Aleart</h5>
            <div class="card-body pt-2">

              <div class="col-12">
                <h6>School Info</h6>
                <hr class="mt-0" />
              </div>
                <ul class="list-unstyled my-3 py-1">
                  <li class="mb-3">
                    <span class="fw-medium">School Name :</span><br>
                    <span>{{ $school->user->name }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">Mobile Number :</span><br>
                    <span>{{ $school->mobile_number }}</span>
                  </li>
                  <li class="mb-3">
                    <span class="fw-medium">Address :</span><br>
                    <span>{{ $school->address ?? 'N/A' }}</span>
                  </li>
                </ul>
              <form id="formValidationExamples" class="row g-6" action="{{ route('school.sos-aleart.store') }}" method="POST">
                @csrf
                <!-- Account Details -->
                <div class="col-12">
                  <h6>Sos Aleart Form</h6>
                  <hr class="mt-0" />
                </div>

                <input type="hidden" name="school_id" value="{{$school->id}}" />
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="emergency-title">Title</label>
                    <input type="text" class="form-control border-2" id="emergency-title" name="title" placeholder="Enter emergency title" required>
                    <small class="text-danger">{{ $errors->first('title') }}</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="emergency-type">{{ __('Type') }}</label>
                    <select class="select2 form-select form-select" data-style="btn-default" id="emergency-type" name="type">
                        @php
                          $sos_types=config('constant.school_sos_alerts');
                        @endphp
                        <option value=""></option>
                        @foreach ($sos_types as $key=>$value)
                          @php
                            $arr = explode(' ',$value,2);
                            $icon = $arr[0];
                            $type = $arr[1];
                          @endphp
                          <option value="{{ $key }}">{{ $icon.' '.__($type) }}</option>
                        @endforeach
                    </select>
                    <small class="text-danger">{{ $errors->first('type') }}</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="emergency-message">Message</label>
                    <textarea class="form-control border-2" id="emergency-message" name="message" rows="4" placeholder="Enter detailed emergency message" required></textarea>
                    <small class="text-danger">{{ $errors->first('message') }}</small>
                </div>
                <div class="col-12">
                  <button type="submit" name="submitButton" class="btn btn-primary me-3 data-submit btn-custom btn-lg px-4 py-2 rounded-pill shadow">Submit</button>
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
