@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Login - Citio')

@section('vendor-script')
@vite(['resources/assets/vendor/js/dropdown-hover.js'])
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
@endsection


@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])

<style>
  .app-brand-logo.demo{
    align-items: center;
    justify-content: center;
    display: flex;
    width: 37px;
    height: 34px;
  }
</style>

@endsection

@section('page-script')
@vite([
  'resources/assets/js/extended-ui-sweetalert2.js',
  'resources/assets/js/modal-enable-otp.js',
])
<script>
  document.getElementById('password').addEventListener('keydown', function (e) {
    if (e.key === ' ') {
      e.preventDefault(); // stop space from being entered
    }
  });

  document.getElementById('password').addEventListener('input', function () {
    this.value = this.value.replace(/\s/g, ''); // remove pasted spaces
  });

    document.addEventListener('DOMContentLoaded', function (e){
    const formAuthentication = document.querySelector('#formAuthentication');
      if (formAuthentication) {
        const fv = FormValidation.formValidation(formAuthentication, {
          fields: {
            email: {
              validators: {
                notEmpty: {
                  message: 'Please enter your email'
                },
                regexp: {
                  regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                  message: 'Please enter a valid email address with domain'
                }
              }
            },
            password: {
              validators: {
                notEmpty: {
                  message: 'Please enter your password'
                },
                stringLength: {
                  min: 8,
                  message: 'Password must be more than 8 characters'
                }
              }
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: '',
              rowSelector: '.mb-6'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),

            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            autoFocus: new FormValidation.plugins.AutoFocus()
          },
          init: instance => {
            instance.on('plugins.message.placed', function (e) {
              if (e.element.parentElement.classList.contains('input-group')) {
                e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
              }
            });
          }
        });
      }
  });
</script>
@if(session('show_otp_modal'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var otpModal = new bootstrap.Modal(document.getElementById('enableOTP'));
        otpModal.show();
    });
</script>
@endif
@endsection

@section('content')

<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{url('/')}}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-register-illustration-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-register-illustration-light.png" data-app-dark-img="illustrations/auth-register-illustration-dark.png">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- login -->
    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">

      <!-- Logo -->
      <div class="app-brand justify-content-center mb-6">
        <a href="{{url('/')}}" class="app-brand-link">
        <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
        <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
        </a>
      </div>
      <!-- /Logo -->
      <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
      <p class="mb-6">Please sign-in to your account and start the adventure</p>

      <form id="formAuthentication" class="mb-4" action="{{route('login')}}" method="POST">
        @csrf
        <div class="mb-6">
          <label for="email" class="form-label">Email</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email Address" autofocus>
          @error('email')
          <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="mb-6 form-password-toggle">
          <label class="form-label" for="password">Password</label>
          <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
          <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
          </div>
        </div>
        <div class="my-8">
          <div class="d-flex justify-content-between">
          <div class="form-check mb-0 ms-2">
            {{-- <input class="form-check-input" type="checkbox" id="remember-me">
            <label class="form-check-label" for="remember-me">
            Remember Me
            </label> --}}
          </div>
          <a href="{{ route('password.request') }}">
            <p class="mb-0">Forgot Password?</p>
          </a>
          </div>
        </div>
        <div class="mb-6">
          <button class="btn btn-primary d-grid w-100 btn-custom" type="submit">Login</button>
        </div>
        </form>

        <p class="text-center">
        <span>New on our platform?</span>
        <a href="{{route('business.register.show')}}">
          <span>Create an account</span>
        </a>
        </p>

      </div>
    </div>
    <!-- /Login -->
  </div>
</div>
@include('_partials/_modals/modal-enable-otp')
@endsection
