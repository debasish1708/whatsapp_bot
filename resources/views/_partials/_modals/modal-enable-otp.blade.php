<!-- Enable OTP Modal -->
<div class="modal fade" id="enableOTP" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Enter One Time Password</h4>
          <p>We have sent an OTP to your email. Please enter it below to verify.</p>
        </div>
        <form id="enableOTPForm" class="row g-5" method="POST" action="{{route('admin.otp.verify')}}">
          @csrf
          <div class="col-12">
            <label class="form-label" for="modalEnableOTP">OTP</label>
            <input type="text" id="modalEnableOTP" name="otp" class="form-control" placeholder="Enter OTP" required />
            @error('otp')
        <span class="text-danger">{{ $message }}</span>
      @enderror
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary me-3">Verify</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
              aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Enable OTP Modal -->
<script>
/**
 * Enable OTP
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const phoneMask = document.querySelector('.phone-number-otp-mask');

    // Phone Number Input Mask
    if (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    }

    // Enable OTP form validation
    FormValidation.formValidation(document.getElementById('enableOTPForm'), {
      fields: {
        otp: {
          validators: {
            notEmpty: {
              message: 'Please enter the OTP'
            },
            stringLength: {
              min: 6,
              max: 6,
              message: 'OTP must be exactly 6 digits'
            },
            regexp: {
              regexp: /^[0-9]+$/,
              message: 'OTP must be numeric'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-12'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          //* Move the error message out of the `input-group` element
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    });
  })();
});
</script>
