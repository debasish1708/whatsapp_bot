@extends('layouts.layoutMaster')

@section('title', 'Home')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  ])
@endsection

@section('page-script')
  @vite('resources/assets/js/app-academy-dashboard.js')
@endsection

@section('content')

  <div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
      <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
        <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total users</span>
            <div class="d-flex align-items-center my-1">
            <h4 class="mb-0 me-2">{{ $total_users }}</h4>
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
      </a>
    </div>
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <div class="content-left">
        <span class="text-heading">Businesses</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">{{ $businesses_count }}</h4>
        </div>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-danger">
          <i class="ti ti-building ti-26px"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <a href="{{ route('admin.pending-accounts') }}" class="text-decoration-none">
        <div class="card">
          <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="content-left">
            <span class="text-heading">Pending Account Reviews</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $pending_reviews_count }}</h4>
            </div>
            </div>
            <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti ti-checklist ti-26px"></i>
            </span>
            </div>
          </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <div class="content-left">
        <span class="text-heading">Subscriptions</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">{{ $subscriptions_count }}</h4>
        </div>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-warning">
          <i class="ti ti-coin ti-26px text-danger"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

@endsection
