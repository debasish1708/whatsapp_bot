@extends('layouts.layoutMaster')

@section('title', __('School Profile'))

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@endsection

<!-- Page Styles -->
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/pages-profile.js'])
@endsection

@section('content')
<!-- Header -->
<div class="row mt-5">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $school->logo ?? asset('assets/img/placeholder.jpg') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6">{{ $school->name }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                {{-- <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-palette ti-lg'></i><span class="fw-medium">UX Designer</span>
                </li> --}}
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  @if($school->address_link)
                    <a href="{{ $school->address_link }}" target="_blank" class="d-flex gap-2 align-items-center text-decoration-none text-dark">
                        <i class="ti ti-map-pin ti-lg"></i>
                        <span class="fw-medium">{{ $school->city ?? 'N/A' }}</span>
                    </a>
                  @else
                    <i class="ti ti-map-pin ti-lg"></i>
                    <span class="fw-medium">{{ $school->city ?? 'N/A' }}</span>
                  @endif
                </li>
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-calendar ti-lg'></i><span class="fw-medium"> Joined {{ $school->created_at->format('d M Y') }}</span></li>
              </ul>
            </div>
            <a href="{{ route('school.profile.edit',$school) }}" class="btn btn-primary me-3 data-submit btn-custom">
              <i class='ti ti-edit ti-xs me-2'></i>{{ __('Edit') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Combined Row for Profile + Education Cards -->
<div class="row g-4">

  <!-- User Profile Card -->
  <div class="col-xl-4 col-lg-5 col-md-5 flex-shrink-0">
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">{{ __('About') }}</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-school ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('School Name') }}:</span>
            <span>{{ $school->name }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-category ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('Type') }}:</span>
            <span>{{ $school->categories->pluck('name')->implode(', ') ?? 'N/A' }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-world ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('Country') }}:</span>
            <span>{{ $school->country ?? 'N/A' }}</span>
          </li>
        </ul>

        <small class="card-text text-uppercase text-muted small">{{ __('Contacts') }}</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-phone-call ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('Contact') }}:</span>
            <span>{{ $school->mobile_number ?? 'N/A' }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-mail ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('Email') }}:</span>
            <span>{{ $school->email ?? 'N/A' }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-location ti-lg"></i>
            <span class="fw-medium mx-2">{{ __('Address') }}:</span>
            <span>{{ $school->address ?? 'N/A' }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Services Offered Card -->
  @if ($school->services)
  <div class="col-xl-4 col-lg-5 col-md-5 flex-shrink-0 d-flex">
  <div class="card mb-4 w-100">
    <div class="card-header">
      <small class="card-text text-uppercase text-muted small">{{ __('Services Offered') }}</small>
    </div>
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2">
      @foreach (json_decode($school->services) as $services)
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
          <i class="ti ti-check me-1"></i> {{ ucwords($services) }}
        </span>
      @endforeach
      </div>
    </div>
  </div>
  </div>
  @endif

</div>


  <!--/ Services Offered Card -->

@endsection
