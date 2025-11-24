@extends('layouts.layoutMaster')

@section('title', __('User List - Pages'))

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss', 'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
  @vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection


@section('page-script')
<script>
  // Inject enum values from PHP to JS
  window.SchoolAdmissionStatus = @json(collect(App\Enums\SchoolAdmissionStatus::cases())->mapWithKeys(fn($case) => [$case->name => $case->value]));
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Status mapping using enum values
    const statusMap = {
      [window.SchoolAdmissionStatus.NEW]: { title: '{{ __('New') }}', class: 'bg-label-warning' },
      [window.SchoolAdmissionStatus.INPROCESS]: { title: '{{ __('In Process') }}', class: 'bg-label-info' },
      [window.SchoolAdmissionStatus.ACCEPTED]: { title: '{{ __('Accepted') }}', class: 'bg-label-success' },
      [window.SchoolAdmissionStatus.REJECTED]: { title: '{{ __('Rejected') }}', class: 'bg-label-danger' }
    };

    var statusFilter = 'all'; // default value

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '{!! route('school.admission.index') !!}',
        data: function (d) {
          d.status = statusFilter; // send status in AJAX request
        }
      },
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'name', name: 'name' },
        {
          data: 'status',
          name: 'status',
          render: function(data, type, row) {
            const status = statusMap[data] || { title: data, class: 'bg-label-secondary' };
            return `<span class="badge ${status.class}">${status.title}</span>`;
          }
        },
        { data: 'payment', name: 'payment' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
      ],
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"' +
              '<"me-2"f>B' +
            '>' +
          '>' +
        '>t' +
        '<"row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
        '>',
      buttons: [],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip()
    });

    $('.nav-pills .nav-link').on('click', function (e) {
      e.preventDefault();
      $('.nav-pills .nav-link').removeClass('active');
      $(this).addClass('active');

      statusFilter = $(this).data('table');

      table.ajax.reload();
    });

    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', '{{ __('Search Application...') }}');
  });
</script>
@endsection

@section('page-style')
<style>
  /* Normal (inactive) button style */
  .nav-pills .nav-link {
    color: #555;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease-in-out;
  }
  /* Active tab gets gradient */
  .nav-pills .nav-link.active {
    background: linear-gradient(135deg, #7F00FF 0%, #E100FF 100%);
    color: #fff !important;
    font-weight: bold;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    transform: scale(1.02);
  }
</style>
@endsection
@section('content')
<!-- Users List Table -->

  <!-- Filter Pills -->
  {{-- <ul class="nav nav-pills mb-3">
  <li class="nav-item">
    <button class="nav-link active" data-table="{{ App\Enums\SchoolAdmissionStatus::ALL->value }}" type="button">{{ __('All') }}</button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-table="{{ App\Enums\SchoolAdmissionStatus::NEW->value }}" type="button">{{ __('New') }}</button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-table="{{ App\Enums\SchoolAdmissionStatus::INPROCESS->value }}" type="button">{{ __('In Process') }}</button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-table="{{ App\Enums\SchoolAdmissionStatus::ACCEPTED->value }}" type="button">{{ __('Accepted') }}</button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-table="{{ App\Enums\SchoolAdmissionStatus::REJECTED->value }}" type="button">{{ __('Rejected') }}</button>
  </li>
</ul> --}}

  <ul class="nav nav-pills mb-3">
    <li class="nav-item">
      <button class="nav-link active" data-table="all" type="button">
        {{ __('All') }}
      </button>
    </li>
    @foreach(App\Enums\SchoolAdmissionStatus::cases() as $case)
      <li class="nav-item">
        <button class="nav-link{{ $loop->first ? ' active' : '' }}" data-table="{{ $case->value }}" type="button">
          {{ __(ucwords(str_replace('_', ' ', strtolower($case->name)))) }}
        </button>
      </li>
    @endforeach
  </ul>



  <!-- DataTables for each filter -->
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead class="border-top">
          <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Payment') }}</th>
            <th>{{ __('Submitted At') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection
