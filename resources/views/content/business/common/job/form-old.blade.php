@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Job Application - School')

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
@vite([
  'resources/assets/js/pages-auth.js'
])
@vite(['resources/assets/js/form-validation.js'])
@vite(['resources/assets/js/forms-pickers.js'])
@endsection

@section('content')
<body class="bg-light">
    <div class="container-xxl py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- School Information Card -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0 text-center">
                            <i class="fas fa-school me-2"></i>
                            School Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-primary border-4">
                                    <i class="fas fa-graduation-cap text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">School Name</small>
                                        <strong class="text-dark">{{ $school->user->name }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-success border-4">
                                    <i class="fas fa-tags text-success fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Type</small>
                                        <strong class="text-dark">{{ $school->categories->pluck('name')->implode(', ') ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-info border-4">
                                    <i class="fas fa-phone text-info fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Mobile Number</small>
                                        <strong class="text-dark">{{ $school->mobile_number }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-warning border-4">
                                    <i class="fas fa-map-marker-alt text-warning fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Address</small>
                                        <strong class="text-dark">{{ $school->address ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Offer Details Card -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0 text-center">
                            <i class="fas fa-briefcase me-2"></i>
                            Job Offer Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-primary border-4">
                                    <i class="fas fa-user-tie text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Position</small>
                                        <strong class="text-dark">{{ $job_offer->position }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-info border-4">
                                    <i class="fas fa-map-marker-alt text-info fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Location</small>
                                        <strong class="text-dark">{{ $job_offer->location }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-success border-4">
                                    <i class="fas fa-dollar-sign text-success fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Salary</small>
                                        <strong class="text-dark">${{ $job_offer->salary }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-warning border-4">
                                    <i class="fas fa-envelope text-warning fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Contact Email</small>
                                        <strong class="text-dark">{{ $job_offer->contact_email }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-danger border-4">
                                    <i class="fas fa-calendar-alt text-danger fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Expiry Date</small>
                                        <strong class="text-dark">{{ $job_offer->expiry_date ? \Carbon\Carbon::parse($job_offer->expiry_date)->format('d M Y') : 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-secondary border-4">
                                    <i class="fas fa-info-circle text-secondary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Status</small>
                                        <span class="badge bg-success fs-6">{{ ucfirst($job_offer->status) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-secondary border-4 mt-4">
                            <i class="fas fa-file-alt text-primary fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block">Job Description</small>
                                <span class="text-dark">{{ $job_offer->description }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Form Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0 text-center">
                            <i class="fas fa-file-alt me-2"></i>
                            Job Application Form
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="formValidationExamples" class="row g-4" action="{{ route('job-offer.storeApplication') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Personal Information Section -->
                            <div class="col-12">
                                <h6 class="text-primary border-bottom border-primary pb-2 mb-4">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                            </div>

                            <!-- Hidden School ID -->
                            <input type="hidden" name="job_offer_id" value="{{ $job_offer->id }}" />

                            <!-- First Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           id="firstName"
                                           name="first_name"
                                           placeholder="John"
                                           value="{{ old('first_name') }}"
                                           required>
                                    <label for="firstName">
                                        <i class="fas fa-user me-2 text-muted"></i>First Name
                                    </label>
                                    @error('first_name')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           id="lastName"
                                           name="last_name"
                                           placeholder="Doe"
                                           value="{{ old('last_name') }}"
                                           required>
                                    <label for="lastName">
                                        <i class="fas fa-user me-2 text-muted"></i>Last Name
                                    </label>
                                    @error('last_name')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           placeholder="john@gmail.com"
                                           value="{{ old('email') }}"
                                           required>
                                    <label for="email">
                                        <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                                    </label>
                                    @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('mobile_number') is-invalid @enderror"
                                           id="mobileNumber"
                                           name="mobile_number"
                                           placeholder="420123456789"
                                           value="{{ old('mobile_number') }}"
                                           required>
                                    <label for="mobileNumber">
                                        <i class="fas fa-phone me-2 text-muted"></i>Mobile Number
                                    </label>
                                    @error('mobile_number')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-text text-danger">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Note: Use the same mobile number as your WhatsApp, including the country code (e.g., 420123456789).
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            {{-- <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="dateOfBirth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth') }}"
                                           max="{{ \Carbon\Carbon::yesterday()->toDateString() }}"
                                           required>
                                    <label for="dateOfBirth">
                                        <i class="fas fa-calendar me-2 text-muted"></i>Date of Birth
                                    </label>
                                    @error('date_of_birth')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                              <label class="form-label" for="flatpickr-date">Date of Birth</label>
                              <input type="text" class="form-control  @error('date_of_birth') is-invalid @enderror" name="date_of_birth" placeholder="YYYY-MM-DD" id="flatpickr-date" value="{{old('date_of_birth')}}"  required />
                              <small class="text-danger">{{ $errors->first('date_of_birth') }}</small>
                              @error('date_of_birth')
                              <p class="text-danger">{{ $message }}</p>
                              @enderror
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label class="form-label text-primary fw-semibold">
                                    <i class="fas fa-venus-mars me-2"></i>Gender
                                </label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="gender"
                                               value="male"
                                               id="male"
                                               {{ old('gender') == 'male' ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label" for="male">
                                            <i class="fas fa-mars text-primary me-1"></i>Male
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="gender"
                                               value="female"
                                               id="female"
                                               {{ old('gender') == 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="female">
                                            <i class="fas fa-venus text-danger me-1"></i>Female
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="gender"
                                               value="others"
                                               id="others"
                                               {{ old('gender') == 'others' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="others">
                                            <i class="fas fa-genderless text-secondary me-1"></i>Others
                                        </label>
                                    </div>
                                </div>
                                @error('gender')
                                <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Resume -->
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="file"
                                           class="form-control @error('resume') is-invalid @enderror"
                                           id="resume"
                                           name="resume"
                                           accept=".pdf,.doc,.docx,.rtf,.txt"
                                           required>
                                    <label for="resume">
                                        <i class="fas fa-file-upload me-2 text-muted"></i>Resume/CV
                                    </label>
                                    @error('resume')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Accepted formats: PDF, DOC, DOCX, RTF, TXT (Max size: 5MB)
                                </div>
                            </div>

                            <!-- Address Information Section -->
                            <div class="col-12 mt-5">
                                <h6 class="text-success border-bottom border-success pb-2 mb-4">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </h6>
                            </div>

                            <!-- City -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('city') is-invalid @enderror"
                                           id="city"
                                           name="city"
                                           placeholder="Prague"
                                           value="{{ old('city') }}"
                                           required>
                                    <label for="city">
                                        <i class="fas fa-city me-2 text-muted"></i>City
                                    </label>
                                    @error('city')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address"
                                              name="address"
                                              placeholder="Enter your full address"
                                              style="height: 100px">{{ old('address') }}</textarea>
                                    <label for="address">
                                        <i class="fas fa-home me-2 text-muted"></i>Full Address
                                    </label>
                                    @error('address')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="col-12 mt-4">
                                <div class="p-3 bg-light rounded-3 border" style="padding-left: 0.2rem !important;">
                                    <div class="form-check d-flex align-items-center m-0" style="gap: 0.75rem;">
                                        <input class="form-check-input @error('terms_and_conditions') is-invalid @enderror"
                                               type="checkbox"
                                               id="termsConditions"
                                               name="terms_and_conditions"
                                               style="width: 1.2em; height: 1.2em; margin-left: -0.25rem;"
                                               {{ old('terms_and_conditions') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label fw-semibold" for="termsConditions" style="flex: 1;">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            I agree to the
                                            <a href="{{ url('/terms-and-conditions') }}" target="_blank" class="text-primary text-decoration-underline">
                                                terms and conditions
                                            </a>
                                        </label>
                                    </div>
                                    @error('terms_and_conditions')
                                    <p class="text-danger mb-0">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 text-center mt-4">
                                <button type="submit"
                                        name="submitButton"
                                        class="btn btn-primary me-3 data-submit btn-custom btn-lg px-5 py-3 rounded-pill shadow">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection
