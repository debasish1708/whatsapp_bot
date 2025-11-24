<!DOCTYPE html>
@php
$menuFixed = ($configData['layout'] === 'vertical') ? ($menuFixed ?? '') : (($configData['layout'] === 'front') ? '' : $configData['headerType']);
$navbarType = ($configData['layout'] === 'vertical') ? ($configData['navbarType'] ?? '') : (($configData['layout'] === 'front') ? 'layout-navbar-fixed': '');
$isFront = ($isFront ?? '') == true ? 'Front' : '';
$contentLayout = (isset($container) ? (($container === 'container-xxl') ? "layout-compact" : "layout-wide") : "");
@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" class="{{ $configData['style'] }}-style {{($contentLayout ?? '')}} {{ ($navbarType ?? '') }} {{ ($menuFixed ?? '') }} {{ $menuCollapsed ?? '' }} {{ $menuFlipped ?? '' }} {{ $menuOffcanvas ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}" dir="{{ $configData['textDirection'] }}" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="{{ $configData['layout'] . '-menu-' . $configData['themeOpt'] . '-' . $configData['styleOpt'] }}" data-style="{{$configData['styleOptVal']}}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>@yield('title') |
    {{ config('variables.templateName') ? config('variables.templateName') : 'TemplateName' }} -
    {{ config('variables.templateSuffix') ? config('variables.templateSuffix') : 'TemplateSuffix' }}
  </title>
  <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/CITIO_LOGO.png') }}" />


  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)
<!-- Custom Cookie Consent CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/cookie-consent.css') }}">

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)
  {{-- {!! app(\Devrabiul\CookieConsent\CookieConsent::class)->styles() !!} --}}
  {{-- {!! CookieConsent::styles() !!} --}}
  {{-- @include('components.custom-cookie-consent') --}}

</head>

<body>

  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  {{-- remove while creating package --}}
  {{-- remove while creating package end --}}

  <!-- Custom Cookie Consent Component -->
  @include('components.cookie-consent')
  <!-- Custom Cookie Consent JS -->
  <script src="{{ asset('js/site-consent.js') }}"></script>

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)
  @if(auth()->check() && auth()->user()->role->slug == 'school')
    @include('_partials._modals.mobile-assistant-lite')
  @endif
  {{-- {!! CookieConsent::scripts() !!} --}}
</body>


  <!-- Firebase and Analytics -->
  <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-analytics.js"></script>

  <script>
    // console.log('Firebase and Analytics initialized');
    // Initialize Firebase
    const firebaseConfig = {
      apiKey: "AIzaSyBq3zTP20-cqkWr-vNZf-3UdImV_PeFwSE",
      authDomain: "citio-guide-using-whatsapp.firebaseapp.com",
      projectId: "citio-guide-using-whatsapp",
      storageBucket: "citio-guide-using-whatsapp.firebasestorage.app",
      messagingSenderId: "880091315174",
      appId: "1:880091315174:web:7cf0235f3c04a540af3427",
      measurementId: "G-NJ21L8PHYE"
    };

    firebase.initializeApp(firebaseConfig);
    const analytics = firebase.analytics();

    // Optional: Log custom event
    console.log('Custom Analytics log',analytics);
    analytics.logEvent('page_view');
  </script>

</html>
