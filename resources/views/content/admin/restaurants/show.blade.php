@extends('layouts.layoutMaster')

@section('title', 'Restaurant Profile - Profile')

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
<style>
  .btn-custom {
    color: #fff; /* Ensures white text by default */
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease-in-out;
    border: none;
    text-decoration: none;
  }

  .btn-back {
    background: linear-gradient(to right, #8e2de2, #4a00e0); /* Purple gradient */
    color: #fff !important; /* Force white text */
  }
</style>
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
  function handleStatusButtons(url, type){
    // let url=$(this).data('url');
    let message=type=='approve' ? 'Are you sure you want to approve the restaurant?' : 'Are you sure you want to reject the restaurant?';

    if(type=='delete'){
      message='Are you sure you want to delete the restaurant?';
    }
    //let url=$(this).data('url');
    let swalOptions = {
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      customClass: {
        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
      },
      buttonsStyling: false
    };

    if(type == 'reject'){
      swalOptions.title = 'Reason';
      swalOptions.input = 'text';
      swalOptions.inputAttributes = { autocapitalize: 'off' };
      swalOptions.inputValidator = (value) => {
        if (!value) {
          return 'You need to provide a reason!';
        }
      };
    }

    Swal.fire(swalOptions).then((result) => {
      if (result.isConfirmed) {
        let ajaxData = {
          _token: "{{ csrf_token() }}"
        };
        if (type == 'reject') {
          ajaxData.reason = result.value;
        }
        // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
        $.ajax({
          method: type=='delete' ? 'DELETE' : 'PUT',
          url: url,
          data: ajaxData,
          success: function (result) {
            localStorage.setItem('show_toast', result.message);
            location.reload();
          },
          error: function (error) {
            toastr.error(error.responseJSON.message, 'Error');
            console.log(error);
          }
        })
      }
    })
  }
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var msg = localStorage.getItem('show_toast');
    if (msg) {
      toastr.success(msg, 'Success');
      localStorage.removeItem('show_toast');
    }
  });
</script>
@endsection

@section('content')
<!-- Header -->
{{-- <h4>Restaurant details</h4> --}}
<div class="w-100 d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0">Restaurant Details</h4>
    <div class="d-flex gap-2">
        @if($restaurant->is_profile_completed && $restaurant->user->status == 'pending')
            <!-- Full Approve Button -->
            <a href="javascript:void(0)" onclick="handleStatusButtons('{{route('restaurants.approve', $restaurant->id)}}', 'approve')"
               class="btn btn-success">
                Approve
            </a>

            <!-- Full Reject Button -->
            <a href="javascript:void(0)" onclick="handleStatusButtons('{{route('restaurants.reject', $restaurant->id)}}', 'reject')"
               class="btn btn-danger">
                Reject
            </a>
        @endif

        <!-- Back Button -->
        <a href="{{ route('restaurants.index') }}" class="btn btn-back btn-custom ms-3">
            Back
        </a>
    </div>
</div>
{{-- <div class="row mt-5">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $user->restaurant->logo }}" alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6">{{$user->name}}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-user-circle ti-lg'></i><span class="fw-medium">{{$user->role->name}}</span>
                </li>
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-map-pin ti-lg'></i><span class="fw-medium">{{$user->restaurant->address}}</span>
                </li>
                <li class="list-inline-item d-flex gap-2 align-items-center">
                  <i class='ti ti-calendar ti-lg'></i><span class="fw-medium"> Joined {{$user->restaurant->created_at->format('d M Y')}}</span></li>
              </ul>
            </div>
            <a href="{{ route('restaurant.profile.edit') }}" class="btn btn-primary mb-1 btn-custom">
              <i class='ti ti-edit ti-xs me-2'></i>Edit
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> --}}
<!--/ Header -->

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">Details</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user ti-lg"></i><span class="fw-medium mx-2">Restaurant Name:</span> <span>{{$restaurant->user->name ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-building ti-lg"></i><span class="fw-medium mx-2">City:</span> <span>{{$restaurant->city ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-map ti-lg"></i><span class="fw-medium mx-2">Country:</span> <span>{{$restaurant->country ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-map-pin-code ti-lg"></i><span class="fw-medium mx-2">Pincode:</span> <span>{{$restaurant->pincode ?? ''}}</span></li>
        </ul>
        <small class="card-text text-uppercase text-muted small">Contacts</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span class="fw-medium mx-2">Contact:</span> <span>{{$restaurant->mobile_number ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4" style="display: inline-block !important; flex-shrink:1;"><i class="ti ti-mail ti-lg"></i><span class="fw-medium mx-2">Email:</span> <small>{{$restaurant->user->email ?? ''}}</small></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user-circle ti-lg"></i><span class="fw-medium mx-2">Profile status:</span>
            <span class="badge {{$restaurant->is_profile_completed ? 'bg-label-success' : 'bg-label-warning'}}">
                {{$restaurant->is_profile_completed ? 'completed' : 'pending'}}
            </span>
          </li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-receipt-dollar ti-lg"></i><span class="fw-medium mx-2">Payment status:</span>
            <span class="badge {{$subscription ? 'bg-label-success' : 'bg-label-warning'}}">
                {{$subscription ? 'completed' : 'pending'}}
            </span>
          </li>
        </ul>
      </div>
    </div>
    <div class="card mb-6">
      <div class="card-body">
        {{-- <small class="card-text text-uppercase text-muted small">Details</small> --}}
        <small class="card-text text-uppercase text-muted small">Timings</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          @if(!$restaurant->timings->isEmpty())
            {{-- @dd($user->restaurant->timings) --}}
            @php
              $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
              $sortedTimings = $restaurant->timings->sortBy(function ($timing) use ($dayOrder) {
                return array_search(strtolower($timing->day), $dayOrder);
              });
            @endphp
            @foreach($sortedTimings as $timing)
              <li class="d-flex justify-content-between mb-4 gap-5">
                <span class="fw-medium me-2">{{$timing->day}}</span>
                <span class="fw-medium me-2 {{$timing->is_closed ? 'text-danger' : ''}}">
                {{  !$timing->is_closed ?
                    \Carbon\Carbon::parse($timing->start_time)->format('h:i A') .' - '. \Carbon\Carbon::parse($timing->end_time)->format('h:i A')
                    : 'closed'}}
                </span>
              </li>
            @endforeach
            @else
            <span>No timings set by restaurant</span>
          @endif

        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <div class="card mb-6">
      <div class="card-body">
        {{-- <small class="card-text text-uppercase text-muted small">Details</small> --}}
        <small class="card-text text-uppercase text-muted small">Restaurant logo</small>

        <ul class="list-unstyled mb-0 mt-3 pt-1">
          {{-- @if($restaurant->sustainabilities)
            @foreach(collect(json_decode($restaurant->sustainabilities))->toArray() as $sustainability)
              <li class="d-flex flex-wrap mb-4"><span class="fw-medium me-2">{{$sustainability}}</span></li>
            @endforeach
          @endif --}}
          <img src="{{$restaurant->logo ?? asset('assets/img/restaurant_placeholder.png') }}" alt="" width="130" height="130"/>
        </ul>
      </div>
    </div>
    <!--/ About User -->
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        {{-- @php
          $restaurant_sustainabilities = config('constant.restaurant_sustainabilities');
        @endphp --}}
        <small class="card-text text-uppercase text-muted small mb-3">Sustainabilities</small>
        <div class="d-flex flex-wrap gap-2 mt-3">
          @if($restaurant->sustainability)
            @foreach($restaurant->sustainability as $sustainability)
              {{-- <li class="d-flex flex-wrap mb-4">
                <span class="fw-medium me-2">{{$restaurant_sustainabilities[$sustainability]}}</span>
              </li> --}}
              <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                <i class="ti ti-check me-1"></i> {{$sustainability->value}}
              </span>
            @endforeach
          @else
            <span>No sustainabilities selected by the restaurant</span>
          @endif
        </div>
        <hr class="my-6 mx-n4" />
        {{-- @php
          $accessibilityKeys = collect(json_decode($restaurant->accessibilities ?? '[]'))->toArray();

          // Combine all defined accessibilities
          $accessibilityOptions = array_merge(
            config('constant.restaurant_accessibilities_basic'),
            config('constant.restaurant_accessibilities_senior_friendly'),
            config('constant.restaurant_accessibilities_student_friendly'),
            config('constant.restaurant_accessibilities_child_friendly')
          );
        @endphp --}}
        <small class="card-text text-uppercase text-muted small">Accessibilities</small>
        <div class="d-flex flex-wrap gap-2 mt-3">
          @if(!empty($restaurant->accessibility))
            @foreach($restaurant->accessibility as $accessibilities)
              {{-- <li class="d-flex flex-wrap mb-4">
                <span class="fw-medium me-2">{{$accessibilityOptions[$key]}}</span>
              </li> --}}
              <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                <i class="ti ti-check me-1"></i> {{$accessibilities->value}}
              </span>
            @endforeach
          @else
            <span>No accessibilities selected by the restaurant</span>
          @endif
        </div>
      </div>
    </div>
    <!--/ About User -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
