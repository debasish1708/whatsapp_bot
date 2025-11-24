@extends('layouts.layoutMaster')

@section('title', __('Restaurant Profile - CITIO'))

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

<!-- Page Styles -->
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])

@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/pages-profile.js'])
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
@endsection

@section('content')
  @if (session()->exists('restaurant_profile'))
    @php
      $data = session()->get('restaurant_profile');
    @endphp
    <div class="d-none" id="updateProfileMessage"
      data-message="{{ $data['message'] }}"
      data-type="{{ $data['type'] }}">
    </div>
  @endif
<!-- Header -->
<div class="row mt-5">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $user->restaurant->logo ?? asset('assets/img/restaurant_placeholder.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6">{{$user->name}}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-user-circle ti-lg'></i><span class="fw-medium">{{$user->role->name}}</span>
                </li>
                {{-- <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-map-pin ti-lg'></i><span class="fw-medium">{{$user->restaurant->address}}</span>
                </li> --}}
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  @if($user->restaurant->address_link)
                    <a href="{{ $user->restaurant->address_link }}" target="_blank" class="d-flex gap-2 align-items-center text-decoration-none text-dark">
                        <i class="ti ti-map-pin ti-lg"></i>
                        <span class="fw-medium">{{ $user->restaurant->city ?? 'N/A' }}</span>
                    </a>
                  @else
                    <i class="ti ti-map-pin ti-lg"></i>
                    <span class="fw-medium">{{ $user->restaurant->city ?? 'N/A' }}</span>
                  @endif
                </li>
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-calendar ti-lg'></i><span class="fw-medium"> {{ __('Joined') }} {{$user->restaurant->created_at->format('d M Y')}}</span></li>
              </ul>
            </div>
            <a href="{{ route('restaurant.profile.edit') }}" class="btn btn-primary mb-1 btn-custom">
              <i class='ti ti-edit ti-xs me-2'></i>{{ __('Edit') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">{{ __('Details') }}</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user ti-lg"></i><span class="fw-medium mx-2">{{ __('Restaurant Name') }}:</span> <span>{{$user->name}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-building ti-lg"></i><span class="fw-medium mx-2">{{ __('City') }}:</span> <span>{{$user->restaurant->city}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-map ti-lg"></i><span class="fw-medium mx-2">{{ __('Country') }}:</span> <span>{{$user->restaurant->country}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-map-pin-code ti-lg"></i><span class="fw-medium mx-2">{{ __('Pincode') }}:</span> <span>{{$user->restaurant->pincode}}</span></li>
        </ul>
        <small class="card-text text-uppercase text-muted small">{{ __('Contacts') }}</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span class="fw-medium mx-2">{{ __('Contact') }}:</span> <span>{{$user->restaurant->mobile_number}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-mail ti-lg"></i><span class="fw-medium mx-2">{{ __('Email') }}:</span> <span>{{$user->email}}</span></li>
        </ul>
      </div>
    </div>
    <div class="card mb-6">
      <div class="card-body">
        {{-- <small class="card-text text-uppercase text-muted small">Details</small> --}}
        <small class="card-text text-uppercase text-muted small">{{ __('Timings') }}</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          @if(!$user->restaurant->timings->isEmpty())
            {{-- @dd($user->restaurant->timings) --}}
            @php
              $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
              $sortedTimings = $user->restaurant->timings->sortBy(function ($timing) use ($dayOrder) {
                return array_search(strtolower($timing->day), $dayOrder);
              });
            @endphp
            @foreach($sortedTimings as $timing)
              <li class="d-flex justify-content-between mb-4 gap-5">
                <span class="fw-medium me-2">{{ __(ucfirst($timing->day)) }}</span>
                <span class="fw-medium me-2 {{$timing->is_closed ? 'text-danger' : ''}}">
                {{  !$timing->is_closed ?
                    \Carbon\Carbon::parse($timing->start_time)->format('H:i') .' - '. \Carbon\Carbon::parse($timing->end_time)->format('H:i')
                    : __('closed')}}
                </span>
              </li>
            @endforeach
          @endif

        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        {{-- <small class="card-text text-uppercase text-muted small">Details</small> --}}
        <small class="card-text text-uppercase text-muted small">{{ __('Sustainabilities') }}</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
            @if($user->restaurant->sustainability && $user->restaurant->sustainability->count())
              @foreach($user->restaurant->sustainability as $sustainability)
                <li class="d-flex flex-wrap mb-4">
                <span class="fw-medium me-2">{{ __(ucfirst($sustainability->value)) }}.</span>
                </li>
              @endforeach
            @endif
        </ul>

        <hr class="my-6 mx-n4" />

        <small class="card-text text-uppercase text-muted small">{{ __('Accessibilities') }}</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
            @if($user->restaurant->accessibility && $user->restaurant->accessibility->count())
              @foreach($user->restaurant->accessibility as $accessibility)
                <li class="d-flex flex-wrap mb-4">
                <span class="fw-medium me-2">{{ __(ucfirst($accessibility->value)) }}.</span>
                </li>
              @endforeach
            @endif
        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
