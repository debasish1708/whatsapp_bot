@extends('layouts.layoutMaster')

@section('title', __('Menu item details'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/scss/pages/ui-carousel.scss',
  ])
  <style>
        .sliderDiv {
            background-repeat: no-repeat !important;
            background-size: contain !important;
        }
    </style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js'
  ])
@endsection

@section('page-script')
@vite('resources/assets/js/app-academy-dashboard.js')
@vite('resources/assets/js/ui-carousel.js')
@endsection

@section('content')

    {{-- <div class=" container mb-2 col row align-items-center">
        <h4>{{__('Menu item details')}}</h4>
    </div> --}}

    <div class="w-100 d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-3">{{ __('Menu Item Details') }}</h4>
        <a href="{{route('menu-items.index')}}" class="btn btn-primary btn-custom">
            {{ __('Back') }}
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <p class=" small text-uppercase text-muted">{{__('Menu item details')}}:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-burger ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('name')}}:</span>
                                </div>
                                <span class="text-break m-2">{{ $menu_item->name }}</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-category ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('category')}}:</span>
                                </div>
                                <span class="text-break m-2">{{ __($menu_item->category->name) }}</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-file-description ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('description')}}:</span>
                                </div>
                                <span class="text-break m-2">{{ $menu_item->description }}</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-coin ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('price')}}:</span>
                                </div>
                                <span class="text-break m-2">{{ $menu_item->price }}</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-tags ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('tags')}}:</span>
                                </div>
                                <span class="text-break m-2">{{ collect(json_decode($menu_item->tags))->join(', ') }}</span>
                            </div>
                        </li>
                        <li class="mb-2 pt-1">
                            <div class="d-flex">
                                <div class="d-flex h-100">
                                    <div class="badge bg-label-primary p-1 m-1 rounded">
                                        <i class='ti ti-calendar ti-sm'></i>
                                    </div>
                                    <span class="fw-semibold me-1 m-2">{{__('created')}}:</span>
                                </div>
                                <span
                                    class="text-break m-2">{{ $menu_item->created_at->format($menu_item->created_at) . '  (' . $menu_item->created_at->diffForHumans() . ')' }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12 mt-md-0 mt-4">
            <div class="card">
                <h6 class="mt-3 ps-3">{{__('Item images')}}:</h6>
                <div class="swiper" id="swiper-with-progress">
                    <div class="swiper-wrapper">
                        @forelse ($menu_item?->images as $content)
                            @php
                                $path = $content->file_name;
                            @endphp
                            <div class="swiper-slide object-cover sliderDiv"
                                style="border-radius:0.375rem;background-image:url({{ $path }})">
                            </div>
                        @empty
                            <div class="w-100 text-center d-flex justify-content-center align-items-center">
                                <span style="text-wrap: pretty;">No images found for this item</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next swiper-button-white custom-icon"></div>
                    <div class="swiper-button-prev swiper-button-white custom-icon"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
