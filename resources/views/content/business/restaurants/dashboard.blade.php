@extends('layouts.layoutMaster')

@section('title', __('Home'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
    ])
    @vite([
        'resources/assets/vendor/libs/animate-css/animate.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    ])
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
    ])
@endsection

@section('page-script')
  @vite('resources/assets/js/app-academy-dashboard.js')
  @if(auth()->check() && auth()->user()->status === App\Enums\BusinessStatus::PENDING->value)
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          title: 'Please Wait',
          text: "You are not verified by Admin",
          icon: 'info',
          showCancelButton: false,
          confirmButtonText: 'Logout',
          customClass: {
            confirmButton: 'btn btn-danger me-3 waves-effect waves-light'
          },
          buttonsStyling: false,
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then(function (result) {
          if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
          }
        });
      });
    </script>
  @endif

  <!-- resources/views/dashboard.blade.php -->
  {{-- @if(auth()->check() && !auth()->user()->is_verified)
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="adminVerifyModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog">
    <div class="modal-content text-center">
      <div class="modal-header">
      <h5 class="modal-title" id="modalTitle">{{ __('Waiting for Admin Approval') }}</h5>
      </div>
      <div class="modal-body">
      {{ __('Your email has been verified. Once the admin approves your account, you\'ll be able to access the system.') }}
      </div>
      <div class="modal-footer justify-content-center">
      <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-danger">{{ __('Close and Logout') }}</button>
      </form>
      </div>
    </div>
    </div>
    </div>
    @endif --}}

    @if (!$is_already_visited)
        <script>
            document.addEventListener('DOMContentLoaded',function(){
                const tour = new Shepherd.Tour({
                    defaultStepOptions: {
                        scrollTo: false,
                        cancelIcon: { enabled: true }
                    },
                    useModalOverlay: true
                });
                const stepsInput = [
                    {
                        title: 'Navbar Navigační lišta',
                        text: 'This is navbar. Here you can change the language of the app, view profile and change your account password. Toto je navigační lišta. Zde můžete změnit jazyk aplikace, zobrazit profil a změnit heslo účtu.',
                        element: '.navbar',
                        position: 'bottom',
                        skipText: 'Skip',
                        nextText: 'Next'
                    },
                    {
                        title: 'Users Uživatelé',
                        text: 'This card shows the number of customers that are connected with your restaurant. Tato karta zobrazuje počet zákazníků, kteří jsou propojeni s vaší restaurací.',
                        element: '.users',
                        position: 'bottom',
                        skipText: 'Skip',
                        nextText: 'Next'
                    },
                    {
                        title: 'Menu items Položky nabídky',
                        text: 'This card shows the number of menu items that are added in your restaurant. Tato karta zobrazuje počet položek menu, které byly ve vaší restauraci přidány.',
                        element: '.menu-items',
                        position: 'bottom',
                        skipText: 'Skip',
                        nextText: 'Next'
                    },
                    {
                        title: 'Announcements Oznámení',
                        text: 'This card shows the number of active announcements of your restaurant. Tato karta zobrazuje počet aktivních oznámení vaší restaurace.',
                        element: '.announcements',
                        position: 'right',
                        skipText: 'Skip',
                        nextText: 'Next'
                    },
                    {
                        title: 'Job offers Nabídky práce',
                        text: 'This card shows the number of active job offers of your restaurant. Tato karta zobrazuje počet aktivních nabídek práce ve vaší restauraci.',
                        element: '.job-offers',
                        position: 'right',
                        nextText: 'Continue',
                        finishText: 'Finish'
                    }
                ];
                const steps = generateTourSteps(stepsInput);
                console.log(steps);
                steps.forEach(step => tour.addStep(step));
                tour.start();
            });
        </script>
    @endif
@endsection

@section('content')
    @if(auth()->user()->status === App\Enums\BusinessStatus::APPROVED->value)
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card users">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">{{ __('Total Users') }}</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $connected_users_count }}</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-user ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card menu-items">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">{{ __('Menu Items') }}</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $menu_items }}</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-tools-kitchen-2 ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card announcements">
                    <div class="card-body">
                        <div class="d-flex align-items-center  justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">{{ __('Upcoming Announcements') }}</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $announcements_count }}</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-speakerphone ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card job-offers">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">{{ __('Job offers') }}</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $job_offers_count }}</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-briefcase ti-26px text-danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
