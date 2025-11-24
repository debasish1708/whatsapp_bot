@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Reset - Password')

@section('vendor-style')
  <!-- Vendor -->
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

@section('page-style')
  <!-- Page -->
  @vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
  ])
  <style>
    .app-brand-logo.demo {
    align-items: center;
    justify-content: center;
    display: flex;
    width: 37px;
    height: 34px;
    }
  </style>
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')

  <script>
    function togglePassword(inputId, iconSpan) {
    const input = document.getElementById(inputId);
    const icon = iconSpan.querySelector('i');

    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('ti-eye-off');
      icon.classList.add('ti-eye');
    } else {
      input.type = 'password';
      icon.classList.remove('ti-eye');
      icon.classList.add('ti-eye-off');
    }
    }
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const formResetPassword = document.querySelector('#formResetPassword');
    if (formResetPassword) {
      const fv = FormValidation.formValidation(formResetPassword, {
      fields: {
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
        },
        password_confirmation: {
        validators: {
          notEmpty: {
          message: 'Please confirm password'
          },
          identical: {
          compare: function () {
            return formResetPassword.querySelector('[name="password"]').value;
          },
          message: 'The password and its confirm are not the same'
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
        rowSelector: '.mb-3'
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
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Login -->
      <div class="card">
      <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-4 mt-2">
        <a href="{{url('/')}}" class="app-brand-link gap-2">
          <span
          class="app-brand-logo demo">@include('_partials.macros', ["height" => 20, "withbg" => 'fill: #fff;'])</span>
          <span class="app-brand-text demo text-body fw-bold ms-1">{{config('variables.templateName')}}</span>
        </a>
        </div>
        <!-- /Logo -->

        <form id="formResetPassword" action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ request()->query('id') }}">
        <!-- Password Field -->
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control" name="password"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
          <span class="input-group-text cursor-pointer" onclick="togglePassword('password', this)">
            <i class="ti ti-eye-off"></i>
          </span>

          </div>
          @error('password')
        <p class="text-danger">{{ $message }}</p>
      @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-3">
          <label class="form-label" for="confirm-password">Confirm Password</label>
          <div class="input-group input-group-merge">
          <input type="password" id="confirm-password" class="form-control" name="password_confirmation"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
          <span class="input-group-text cursor-pointer" onclick="togglePassword('confirm-password', this)">
            <i class="ti ti-eye-off"></i>
          </span>

          </div>
        </div>

        <div class="mb-3">
          <button class="btn btn-primary d-grid w-100 btn-custom" type="submit">Change Password</button>
        </div>

        </form>
      </div>
      </div>
      <!-- /Register -->
    </div>
    </div>
  </div>
@endsection