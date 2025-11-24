@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', __('Payment'))

{{-- Page Styles --}}
@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-payment.scss'])
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/cleavejs/cleave.js'])
  @vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite([
  'resources/assets/vendor/libs/block-ui/block-ui.js'
])
@endsection

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@vite([
  'resources/assets/vendor/libs/spinkit/spinkit.scss'
])
@endsection
{{-- Page Scripts --}}
@section('page-script')
  @vite([
    'resources/assets/js/pages-pricing.js',
    'resources/assets/js/front-page-payment.js'
  ])
<!-- Place this at the bottom of the blade file, before </body> -->
<script>
  const cancelSubscriptionBtn = document.querySelector('#cancel-subscription');
  if (cancelSubscriptionBtn) {
    cancelSubscriptionBtn.onclick = function (e) {
      e.preventDefault();
      Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('You want to cancel your subscription?') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes, cancel it!') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.isConfirmed) {
          // Show block UI when user confirms cancellation
          $('#card-block').block({
            message:
              '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
            css: {
              backgroundColor: 'transparent',
              color: '#fff',
              border: '0'
            },
            overlayCSS: {
              opacity: 0.5
            }
          });

          // Submit the form - the loading will continue until page reloads or response is received
          cancelSubscriptionBtn.closest('form').submit();
        }
      });
    };
  }
</script>
@endsection

@section('content')
  <section class="section-py bg-body first-section-pt">
    <div class="card p-4 p-md-5 shadow-sm" id="card-block">
    <div class="row g-4 align-items-center">

      <div class="col-lg-6">
      <h3 class="mb-3">{{ __('Your Subscription') }}</h3>

      @if(auth()->user()->subscribed('business'))
      <div class="alert alert-success">
      <strong>{{ __('You\'re currently subscribed!') }}</strong><br>
      {{ __('Next billing date:') }}
      <strong>{{ \Carbon\Carbon::createFromTimestamp(auth()->user()->subscription('business')->asStripeSubscription()->current_period_end)->toFormattedDateString() }}</strong>
      </div>
      @php
        $subscription = auth()->user()->subscription('business');
      @endphp
      @if (!$subscription->onGracePeriod())
        <form method="POST" action="{{ route('subscribe.cancel') }}">
        @csrf
        <button class="btn btn-danger btn-card-block-custom" id="cancel-subscription">
        <i class="ti ti-trash"></i> {{ __('Cancel Subscription') }}
        </button>
        </form>
      @endif
      {{-- <a href="{{ route('billing.portal') }}" class="btn btn-outline-primary mt-3">--}}
      {{-- {{ __('Manage Billing') }}--}}{{-- </a>--}}
    @else
      <div class="alert alert-warning">
      <strong>{{ __('You are not subscribed yet.') }}</strong><br>
      {{ __('Start your subscription today for full access.') }}
      </div>

      {{-- <a href="{{ route('subscribe') }}" class="btn btn-primary mt-3">--}}
      {{-- {{ __('Subscribe Monthly') }}--}}{{-- </a>--}}
    @endif
      </div>

      <div class="col-lg-6 border-start-lg">
      <h4 class="mb-3">{{ __('Order Summary') }}</h4>
      <p class="text-muted">
        {{ __('Get full access to all features and future updates.') }}
      </p>

      <div class="bg-light p-4 rounded mb-4">
        <div class="d-flex justify-content-between align-items-center">
        <span>{{ __('Monthly Plan') }}</span>
        <strong>10.00 CZK / {{ __('month') }}</strong>
        </div>
      </div>

      <ul class="list-unstyled mb-4">
        <li class="d-flex justify-content-between">
        <span>{{ __('Subtotal') }}</span>
        <strong>10.00 CZK</strong>
        </li>
        <li class="d-flex justify-content-between">
        <span>{{ __('Tax') }}</span>
        <strong>0.00 CZK</strong>
        </li>
        <hr>
        <li class="d-flex justify-content-between">
        <span>{{ __('Total') }}</span>
        <strong>10.00 CZK</strong>
        </li>
      </ul>

      @unless(auth()->user()->subscribed('business'))
      <form method="POST" action="{{ route('subscribe.create') }}">
      @csrf
      <button class="btn btn-success w-100">
      <span>{{ __('Proceed with Payment') }}</span>
      <i class="ti ti-arrow-right ms-2"></i>
      </button>
      </form>
    @endunless

      <p class="text-muted small mt-3">
        {{ __('By continuing, you agree to our') }} <a href="#">{{ __('Terms of Service') }}</a> {{ __('and') }} <a
        href="#">{{ __('Privacy Policy') }}</a>.<br>
        {{ __('Note: Payments are non-refundable.') }}
      </p>
      </div>
    </div>
    </div>
  </section>
@endsection
