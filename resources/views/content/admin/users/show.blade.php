@extends('layouts.layoutMaster')

@section('title', 'User Profile - Profile')

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
<div class="w-100 d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0">User Details</h4>
    <a href="{{route('admin.users.index')}}" class="btn btn-primary btn-custom">
        Back
    </a>
</div>

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">Details</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user ti-lg"></i><span class="fw-medium mx-2">Name:</span> <span>{{$user->name}}</span></li>
          @if (isset($user->email))
            <li class="d-flex align-items-center mb-4"><i class="ti ti-mail ti-lg"></i><span class="fw-medium mx-2">Email:</span> <span>{{$user->email}}</span></li>
          @endif
          {{-- <li class="d-flex align-items-center mb-4"><i class="ti ti-map ti-lg"></i><span class="fw-medium mx-2">Country:</span> <span>{{$school->country}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-map-pin-code ti-lg"></i><span class="fw-medium mx-2">Pincode:</span> <span>{{$school->pincode}}</span></li> --}}
        </ul>
        <small class="card-text text-uppercase text-muted small">Contacts</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span class="fw-medium mx-2">Contact:</span> <span>{{$user->mobile_number}}</span></li>
          {{-- <li class="d-flex align-items-center mb-4"><i class="ti ti-mail ti-lg"></i><span class="fw-medium mx-2">Email:</span> <span>{{$school->user->email}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user-circle ti-lg"></i><span class="fw-medium mx-2">Profile status:</span> --}}
            {{-- <span class="badge {{$school->is_profile_completed ? 'bg-label-success' : 'bg-label-warning'}}">
                {{$school->is_profile_completed ? 'completed' : 'pending'}}
            </span> --}}
          </li>
        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
  </div>
</div>
<!--/ User Profile Content -->
@endsection
