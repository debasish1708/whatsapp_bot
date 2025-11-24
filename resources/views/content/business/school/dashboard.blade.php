@extends('layouts.layoutMaster')

@section('title', __('Academy Dashboard - Apps'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@endsection

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/cards-advance.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  ])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
  @vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  ])
@endsection

@section('page-script')
@vite('resources/assets/js/app-academy-dashboard.js')
{{-- @vite([
  'resources/assets/js/dashboards-analytics.js'
]) --}}
  {{-- <script>
     function initSupportTracker() {
      // your whole code here
      let cardColor, headingColor, labelColor, shadeColor, grayColor;
        if (isDarkStyle) {
          cardColor = config.colors_dark.cardColor;
          labelColor = config.colors_dark.textMuted;
          headingColor = config.colors_dark.headingColor;
          shadeColor = 'dark';
          grayColor = '#5E6692'; // gray color is for stacked bar chart
        } else {
          cardColor = config.colors.cardColor;
          labelColor = config.colors.textMuted;
          headingColor = config.colors.headingColor;
          shadeColor = '';
          grayColor = '#817D8D';
        }

      // Support Tracker - Radial Bar Chart
      // --------------------------------------------------------------------
      const supportTrackerEl = document.querySelector('#supportTracker');
      // Calculate percentage in PHP and pass to JS
      const paidAdmissions = {{ $paid_admissions_count }};
      const totalAdmissions = {{ $admissions_count }};
      let percentage = 0;
      if (totalAdmissions > 0) {
        percentage = Math.round((paidAdmissions / totalAdmissions) * 100);
      }

      const supportTrackerOptions = {
        series: [percentage],
        labels: ['% Paid Admissions'],
        chart: {
          height: 360,
          type: 'radialBar'
        },
        plotOptions: {
          radialBar: {
            offsetY: 10,
            startAngle: -140,
            endAngle: 130,
            hollow: {
              size: '65%'
            },
            track: {
              background: cardColor,
              strokeWidth: '100%'
            },
            dataLabels: {
              name: {
                offsetY: -20,
                color: labelColor,
                fontSize: '13px',
                fontWeight: '400',
                fontFamily: 'Public Sans'
              },
              value: {
                offsetY: 10,
                color: headingColor,
                fontSize: '38px',
                fontWeight: '500',
                fontFamily: 'Public Sans',
                formatter: function(val) {
                  return val + '%';
                }
              }
            }
          }
        },
        colors: [config.colors.primary],
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            shadeIntensity: 0.5,
            gradientToColors: [config.colors.primary],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 0.6,
            stops: [30, 70, 100]
          }
        },
        stroke: {
          dashArray: 10
        },
        grid: {
          padding: {
            top: -20,
            bottom: 5
          }
        },
        states: {
          hover: {
            filter: {
              type: 'none'
            }
          },
          active: {
            filter: {
              type: 'none'
            }
          }
        },
        responsive: [
          {
            breakpoint: 1025,
            options: {
              chart: {
                height: 330
              }
            }
          },
          {
            breakpoint: 769,
            options: {
              chart: {
                height: 280
              }
            }
          }
        ]
      };
      if (typeof supportTrackerEl !== undefined && supportTrackerEl !== null) {
        const supportTracker = new ApexCharts(supportTrackerEl, supportTrackerOptions);
        supportTracker.render();
      }
    }
    document.addEventListener('DOMContentLoaded', initSupportTracker);
  </script> --}}
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
    @if(!$is_already_visited)
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
                    title: "Navbar Navigační lišta",
                    text: "This is navbar. Here you can change the language of the app, view and edit profile. Toto je navigační lišta. Zde můžete změnit jazyk aplikace, zobrazit profil a změnit heslo účtu.",
                    element: '.navbar',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "Users Uživatelé",
                    text: "This card shows the number of customers that are connected with your school. Tato karta ukazuje počet zákazníků připojených k vaší škole.",
                    element: '.users',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "Form Applications Přihlášky",
                    text: "This card shows the number of pending admission forms in your school. Tato karta ukazuje počet čekajících přihlášek ke studiu ve vaší škole.",
                    element: '.form-applications',
                    position: 'bottom',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "Announcements Oznámení",
                    text: "This card shows the number of active announcements of your school. Tato karta ukazuje počet aktivních oznámení vaší školy.",
                    element: '.announcements',
                    position: 'right',
                    skipText: 'Skip',
                    nextText: 'Next'
                },
                {
                    title: "Clubs Kluby",
                    text: "This card shows the number of clubs of your school. Tato karta ukazuje počet klubů vaší školy.",
                    element: '.clubs',
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
<!-- resources/views/dashboard.blade.php -->
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
    <div class="card form-applications">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Form Applications') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $total_applications }}</h4>
            </div>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="ti ti-file-description ti-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card announcements">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
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
    <div class="card clubs">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ __('Total Clubs') }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $clubs_count }}</h4>
            </div>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti ti-star ti-26px text-danger"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- <!-- Support Tracker -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">User Analytics</h5>
          <p class="card-subtitle">All Time</p>
        </div>
      </div>
      <div class="card-body row">
        <div class="col-12 col-sm-4 col-md-12 col-lg-4">
          <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
            <h2 class="mb-0">{{ $total_users + $admissions_count + $job_applications_count + $paid_admissions_count }}</h2>
            <p class="mb-0">Users</p>
          </div>
          <ul class="p-0 m-0">
            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
              <div class="badge rounded bg-label-primary p-1_5"><i class="ti ti-user ti-md"></i></div>
              <div>
                <h6 class="mb-0 text-nowrap">Total Searches</h6>
                <small class="text-muted">{{ $total_users }}</small>
              </div>
            </li>
            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
              <div class="badge rounded bg-label-info p-1_5"><i class="ti ti-file-description ti-md"></i></div>
              <div>
                <h6 class="mb-0 text-nowrap">Admission Request</h6>
                <small class="text-muted">{{ $admissions_count }}</small>
              </div>
            </li>
            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
              <div class="badge rounded bg-label-success p-1_5"><i class="ti ti-briefcase ti-md"></i></div>
              <div>
                <h6 class="mb-0 text-nowrap">Jobs Request</h6>
                <small class="text-muted">{{ $job_applications_count }}</small>
              </div>
            </li>
            <li class="d-flex gap-4 align-items-center pb-1">
              <div class="badge rounded bg-label-warning p-1_5"><i class="ti ti-currency-dollar ti-md"></i></div>
              <div>
                <h6 class="mb-0 text-nowrap">Paid Admission Fees</h6>
                <small class="text-muted">{{ $paid_admissions_count }}</small>
              </div>
            </li>
          </ul>
        </div>
        <div class="col-12 col-sm-8 col-md-12 col-lg-8">
          <div id="supportTracker"></div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Support Tracker --> --}}
@endif

@endsection
