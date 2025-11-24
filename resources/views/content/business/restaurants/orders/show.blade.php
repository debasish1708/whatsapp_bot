@extends('layouts.layoutMaster')

@section('title', 'Order Details - Orders')

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
        console.log(url);
      // let url=$(this).data('url');
      let message=type=='delivered' ? "{{__('Are you sure you want to mark order as delivered?')}}" : "{{__('Are you sure you want to cancel the order?')}}";

      let swalOptions = {
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      };

      //let url=$(this).data('url');
      Swal.fire(swalOptions).then((result) => {
        if (result.isConfirmed) {

          let ajaxData = {
            _token: "{{ csrf_token() }}"
          };
          // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
          $.ajax({
            method: 'PUT',
            url: url,
            data: ajaxData,
            success: function (result) {
              toastr.success(result.message, 'Success');
              $('.datatables-users').DataTable().ajax.reload(null, false);
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
<div class="w-100 d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0">{{__('Order Details')}}</h4>
    <div class="d-flex gap-2">
        @if($order->status == 'pending')
            <!-- Full Approve Button -->
            <a href="javascript:void(0)" onclick="handleStatusButtons('{{route('restaurant.orders.mark-delivered', $order->id)}}', 'delivered')"
               class="btn btn-success">
                {{__('Mark as Delivered')}}
            </a>

            <!-- Full Reject Button -->
            <a href="javascript:void(0)" onclick="handleStatusButtons('{{route('restaurant.orders.mark-canceled', $order->id)}}', 'canceled')"
               class="btn btn-danger">
                {{__('Cancel Order')}}
            </a>
        @endif

        <!-- Back Button -->
        <a href="{{ route('restaurant.orders.index') }}" class="btn btn-back btn-custom ms-3">
            {{__('Back')}}
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
        <small class="card-text text-uppercase text-muted small">{{__('User Details')}}:</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user ti-lg"></i><span class="fw-medium mx-2">{{__('name')}}e:</span> <span>{{$order->user->name ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span class="fw-medium mx-2">{{__('Contact')}}:</span> <span>{{$order->user->mobile_number ?? ''}}</span></li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-user-circle ti-lg"></i><span class="fw-medium mx-2">{{__('status')}}:</span>
            <span class="badge {{$order->status == 'pending' ? 'bg-label-warning' : ($order->status =='delivered' ? 'bg-label-success' : 'bg-label-danger')}}">
                {{$order->status}}
            </span>
          </li>
          <li class="d-flex align-items-center mb-4"><i class="ti ti-receipt-dollar ti-lg"></i><span class="fw-medium mx-2">{{__('payment status')}}:</span>
            <span class="badge {{$order->payment_status == 'paid' ? 'bg-label-success' : 'bg-label-warning'}}">
                {{$order->payment_status}}
            </span>
          </li>
        </ul>
      </div>
    </div>
    {{-- <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">Timings</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          @if(!$restaurant->timings->isEmpty())
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
          @endif

        </ul>
      </div>
    </div> --}}
    <!--/ About User -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!--/ About User -->
    <!-- About User -->
    <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">{{__('Order items')}}</small>
        <div class="w-100 d-flex flex-wrap gap-2 mt-3">
          <ul class="w-75 list-unstyled mb-0 mt-3 pt-1">
          @if($order->cart)
              <li class="d-flex justify-content-between mb-4 gap-5">
                <span class="fw-medium me-2">{{__('Item name')}}:</span>
                <span class="fw-medium me-2">
                {{__('Quantity')}}
                </span>
                <span class="fw-medium me-2">
                {{__('Price')}}
                </span>
              </li>
            @foreach($order->cart as $cart)
              <li class="d-flex justify-content-between mb-4 gap-5">
                <span class="fw-medium me-2">{{$cart->restaurantMenuItem->name}}</span>
                <span class="fw-medium me-2">
                {{ $cart->quantity }}
                </span>
                <span class="fw-medium me-2">
                {{ $cart->price }} CZK
                </span>
              </li>
            @endforeach
          @else
            <span>No services selected by the school</span>
          @endif
          <hr class="my-6 mx-n4" />
            <li class="d-flex justify-content-between mb-4 gap-5">
                <span class="fw-medium me-2">{{__('Total')}}: </span>
                <span class="fw-medium me-2">
                {{ $order->total_amount }} CZK
                </span>
              </li>
            </div>
          </ul>
{{--
        <hr class="my-6 mx-n4" />

        <small class="card-text text-uppercase text-muted small">Accessibilities</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          @if($restaurant->accessibilities)
            @foreach(collect(json_decode($restaurant->accessibilities))->toArray() as $accessibility)
              <li class="d-flex flex-wrap mb-4"><span class="fw-medium me-2">{{$accessibility}}</span></li>
            @endforeach
          @else
            <span>No accessibilities selected by the restaurant</span>
          @endif

        </ul> --}}
      </div>
    </div>
    <!--/ About User -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
