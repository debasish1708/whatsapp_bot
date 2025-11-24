@extends('layouts.layoutMaster')

@section('title', __('Change Password - Citio'))

@section('vendor-style')
<!-- Vendor -->
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite(['resources/assets/vendor/js/dropdown-hover.js'])
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded',function(e){
        const formAuthentication = document.querySelector('#formAuthentication');
        (function () {
            // Form validation for Add new record
            if (formAuthentication) {
                const fv = FormValidation.formValidation(formAuthentication, {
                    fields: {
                        password: {
                            validators: {
                                notEmpty: {
                                    message: '{{__("current password is required")}}'
                                },
                            }
                        },
                        newpass: {
                            validators: {
                                notEmpty: {
                                    message: '{{__("New password is required")}}'
                                },
                                stringLength: {
                                    min: 8,
                                    message: '{{__("Password must be at least 8 characters.")}}'
                                }
                            }
                        },
                        'newpass_confirmation': {
                            validators: {
                                notEmpty: {
                                    message: '{{__("confirm passowrd is required")}}'
                                },
                                identical: {
                                    compare: function () {
                                    return formAuthentication.querySelector('[name="newpass"]').value;
                                    },
                                    message: '{{__("Confirm password must match to new password.")}}'
                                },
                                // stringLength: {
                                //   min: 6,
                                //   message: 'Password must be more than 6 characters'
                                // }
                            }
                        },
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

            //  Two Steps Verification
            const numeralMask = document.querySelectorAll('.numeral-mask');

            // Verification masking
            if (numeralMask.length) {
                numeralMask.forEach(e => {
                    new Cleave(e, {
                    numeral: true
                    });
                });
            }
        })();
    })
</script>
@endsection

@section('content')
<div class="col-xl">
    <div class="card col-md-6 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Change password') }}</h5>
        </div>
        <div class="card-body">
            <form id="formAuthentication" class="mb-3" action="{{route('change-password.update')}}" method="post">
                @csrf
                @method('put')
                <div class="mb-3 form-password-toggle">
                    <label for="select2Multiple" class="text-lg">{{ __('Current password') }}</label>
                    <div class=" input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    @error('password')
                      <p class="text-danger">
                        {{$message}}
                      </p>
                    @enderror
                </div>
                <div class="mb-3 form-password-toggle">
                    <label for="newpass" class="text-lg">{{ __('New password') }}</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="newpass" class="form-control" name="newpass" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="newpass" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    @error('newpass')
                      <p class="text-danger">
                        {{$message}}
                      </p>
                    @enderror
                </div>
                <div class="mb-3 form-password-toggle">
                    <label for="newpass_confirm" class="text-lg">{{ __('Confirm password') }}</label>
                    <div class="input-group input-group-merge">
                      <input type="password" id="newpass_confirm" class="form-control" name="newpass_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="newpass_confirm" />
                      <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                </div>
                <button  class="btn btn-primary btn-custom">{{ __('Change') }}</button>
                <!-- <a href="/home" class="btn btn-primary">Change</a> -->
            </form>
        </div>
    </div>
</div>
@endsection