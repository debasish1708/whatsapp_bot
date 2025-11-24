<!-- BEGIN: Vendor JS-->

@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/node-waves/node-waves.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/js/menu.js',
  'resources/assets/vendor/libs/toastr/toastr.js',
  'resources/assets/vendor/libs/shepherd/shepherd.js'
])
<script>
  document.addEventListener('DOMContentLoaded',function(e){
    toastr.options = {
      closeButton: true,
      debug: false,
      newestOnTop: true,
      progressBar: false,
      positionClass: 'toast-top-right',
      preventDuplicates: false,
      onclick: null,
      showDuration: 300,
      hideDuration: 1000,
      timeOut: 6000, // ← Increased from 1000 to 3000
      extendedTimeOut: 1000, // ← Increased for better visibility
      showEasing: 'swing',
      hideEasing: 'linear',
      showMethod: 'fadeIn',
      hideMethod: 'fadeOut'
    };
    @if (session('success'))
      toastr.success("{{session('success')}}", "{{ __('Success') }}");
    @elseif(session('warning'))
      toastr.warning("{{session('warning')}}", "{{ __('Warning') }}");
    @elseif(session('info'))
      toastr.info("{{session('info')}}", "{{ __('Info') }}");
    @elseif(session('error'))
      toastr.error("{{session('error')}}", "{{ __('Error') }}");
    @endif
  });
</script>
<script>
    function generateTourSteps(stepsInput) {
        const backBtnClass = 'btn btn-sm btn-label-secondary md-btn-flat waves-effect waves-light';
        const nextBtnClass = 'btn btn-sm btn-primary btn-next waves-effect waves-light';
        return stepsInput.map((step, index) => {
            const buttons = [];
            if (step.showSkip !== false) {
                buttons.push({
                    action: step.skipAction || function() { this.cancel(); },
                    classes: step.backBtnClass || backBtnClass,
                    text: step.skipText || 'Skip'
                });
            }
            if (index < stepsInput.length - 1) {
                buttons.push({
                    text: step.nextText || 'Next',
                    classes: step.nextBtnClass || nextBtnClass,
                    action: step.nextAction || function() { this.next(); }
                });
            } else if (step.finishText) {
                buttons.push({
                    text: step.finishText,
                    classes: step.finishBtnClass || nextBtnClass,
                    action: step.finishAction || function() { this.complete(); }
                });
            }
            return {
                title: step.title,
                text: step.text,
                attachTo: step.element ? { element: step.element, on: step.position || 'bottom' } : undefined,
                buttons
            };
        });
    }
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if (session('modal_success'))
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('modal_success') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-success'
        },
        buttonsStyling: false
      });
    @elseif (session('modal_warning'))
      Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: "{{ session('modal_warning') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-warning'
        },
        buttonsStyling: false
      });
    @elseif (session('modal_info'))
      Swal.fire({
        icon: 'info',
        title: 'Information',
        text: "{{ session('modal_info') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-info'
        },
        buttonsStyling: false
      });
    @elseif (session('modal_error'))
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: "{{ session('modal_error') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-danger'
        },
        buttonsStyling: false
      });
    @elseif (session('subscription'))
      Swal.fire({
        icon: 'info',
        title: 'Info!',
        text: "{{ session('subscription') }}",
        customClass: {
          confirmButton: 'btn waves-effect waves-light btn-info'
        },
        buttonsStyling: false
      });
    @endif
  });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        $("#delete-request").on('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "This will remove your account, and all related details permanently",
                customClass: {
                    confirmButton: 'btn waves-effect waves-light btn-warning'
                },
                buttonsStyling: false
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        method: "post",
                        url: "{{route('accounts.request-delete')}}",
                        data:{
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(result){
                            Swal.fire({
                                icon: 'success',
                                title: 'success!',
                                text: result.message,
                                customClass: {
                                    confirmButton: 'btn waves-effect waves-light btn-success'
                                },
                                buttonsStyling: false
                            })
                        },
                        error: function(error){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.responseJSON.message,
                                customClass: {
                                    confirmButton: 'btn waves-effect waves-light btn-danger'
                                },
                                buttonsStyling: false
                            })
                        }
                    });
                }
            });
        });
    });
</script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
