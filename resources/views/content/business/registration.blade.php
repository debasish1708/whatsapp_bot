@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Register - Citio')

@section('vendor-script')
@vite(['resources/assets/vendor/js/dropdown-hover.js'])
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection


@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])

<style>
  .app-brand-logo.demo{
    align-items: center;
    justify-content: center;
    display: flex;
    width: 37px;
    height: 34px;
  }
</style>

@endsection

@section('page-script')
@vite([
  'resources/assets/js/forms-selects.js',
  'resources/assets/js/forms-tagify.js',
  'resources/assets/js/forms-typeahead.js',
])
@vite([
  'resources/assets/js/extended-ui-sweetalert2.js'
])
@vite([
  'resources/js/place-search.js'
]);


<script>
  document.addEventListener('DOMContentLoaded', function (e){
    const formAuthentication = document.querySelector('#formAuthentication');
  if (formAuthentication) {
    const fv = FormValidation.formValidation(formAuthentication, {
      fields: {
        'category': {
          validators: {
            notEmpty: {
              message: 'Please Select an option'
            },
          }
        },
        'business_name': {
          validators: {
            notEmpty: {
              message: 'Please enter business name'
            },
            stringLength: {
              min: 3,
              message: 'Business name must be more than 3 characters'
            }
          }
        },
        email: {
          validators: {
            notEmpty: {
              message: 'Please enter your email'
            },
            regexp: {
              regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
              message: 'Please enter a valid email address with domain'
            }
          }
        },
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
        'password_confirmation': {
          validators: {
            notEmpty: {
              message: 'Please confirm password'
            },
            identical: {
              compare: function () {
                return formAuthentication.querySelector('[name="password"]').value;
              },
              message: 'The password and its confirm are not the same'
            },
            stringLength: {
              min: 8,
              message: 'Password must be more than 8 characters'
            }
          }
        },
        terms: {
          validators: {
            notEmpty: {
              message: 'Please agree terms & conditions'
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

<script>
  function changeCategory(category) {
    const placeholderMap = {
      'Restaurant': 'Enter Your Restaurant Name',
      'Museum': 'Enter Your Museum Name',
      'Hotel': 'Enter Your Hotel Name',
      'School': 'Enter Your School Name'
    };
    const input = document.getElementById('business_name');
    input.placeholder = placeholderMap[category] || 'Enter your Business Name';

    // Show or hide school and restaurant subcategory dropdowns
    const schoolRow = document.getElementById('schoolSubcategoryRow');
    const restaurantRow = document.getElementById('RestaurantCategory');
    const schoolSelect = document.getElementById('selectSchoolCategory');
    const restaurantSelect = document.getElementById('select2Multiple');

    if (category === 'School') {
      schoolRow.classList.remove('d-none');
      restaurantRow.classList.add('d-none');
      // Clear restaurant select when switching to school
      if (restaurantSelect) restaurantSelect.value = '';
      if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(restaurantSelect).val(null).trigger('change');
      }
    } else if (category === 'Restaurant') {
      restaurantRow.classList.remove('d-none');
      schoolRow.classList.add('d-none');
      // Clear school select when switching to restaurant
      if (schoolSelect) schoolSelect.value = '';
      if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(schoolSelect).val(null).trigger('change');
      }
    } else {
      schoolRow.classList.add('d-none');
      restaurantRow.classList.add('d-none');
      // Clear both selects when hidden
      if (schoolSelect) schoolSelect.value = '';
      if (restaurantSelect) restaurantSelect.value = '';
      if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(schoolSelect).val(null).trigger('change');
        window.jQuery(restaurantSelect).val(null).trigger('change');
      }
    }
  }
  document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('selectpickerBasic');
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex].dataset.name;
    changeCategory(selectedCategory);
  });
</script>

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

@endsection

@section('page-style')
<style>
  .suggestion-box {
    position: absolute;
    width: 100%;
    z-index: 1000;
    max-height: 250px;
    overflow-y: auto;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e2e6ea;
    margin-top: 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .suggestion-box .list-group-item {
    border: none;
    padding: 10px 16px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: background 0.2s ease-in-out;
  }

  .suggestion-box .list-group-item:hover,
  .suggestion-box .list-group-item.active {
    background-color: #f1f3f5;
    font-weight: 500;
    color: #007bff;
  }

  .suggestion-box .list-group-item.text-muted {
    font-style: italic;
    color: #6c757d;
  }
</style>
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{url('/')}}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-8 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-register-illustration-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-register-illustration-light.png" data-app-dark-img="illustrations/auth-register-illustration-dark.png">

        <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-register-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Register -->
    <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Welcome to Citio 🚀</h4>
        <p class="mb-6">Make your app management easy and fun!</p>

        <form id="formAuthentication" class="mb-6" action="{{ route('business.register.store') }}" method="POST">
            @csrf
            <!-- Category Selection -->
            {{-- <div class="mb-3">
             <label class="form-label fw-semibold text-dark">
                Category
             </label>
             <div class="dropdown">
               <button type="button" class="btn btn-outline-primary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" id="categoryButton">
                 <span>Select Category</span>
               </button>
               <ul class="dropdown-menu w-100 shadow border-0">
                 <li><h6 class="dropdown-header text-primary">Choose your business type</h6></li>
                 <li><hr class="dropdown-divider"></li>
                 <li><a class="dropdown-item py-2" href="javascript:void(0);" onclick="changeCategory('Restaurant')">
                    Restaurant
                 </a></li>
                 <li><a class="dropdown-item py-2" href="javascript:void(0);" onclick="changeCategory('Museum')">
                    Museum
                 </a></li>
                 <li><a class="dropdown-item py-2" href="javascript:void(0);" onclick="changeCategory('Hotel')">
                    Hotel
                 </a></li>
                 <li><a class="dropdown-item py-2" href="javascript:void(0);" onclick="changeCategory('School')">
                    School
                 </a></li>
               </ul>
               <input type="hidden" name="category" id="selectedCategory">
               @error('category')
                  <p class="text-danger">{{ $errors->first('category') }}</p>
                @enderror
             </div> --}}
              {{-- <div class="mb-3" id="schoolSubcategoryWrapper">
                <label for="selectpickerBasic" class="form-label fw-semibold text-dark">
                  Category
                </label>
                <select class="selectpicker w-100 border-2" id="selectpickerBasic" name="category" data-style="btn-default">
                  <option value="">Select your business type</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
                </select>
                @error('category')
                  <p class="text-danger">{{ $errors->first('school_subcategory') }}</p>
                @enderror
              </div> --}}

              <div class="mb-3" id="categoryWrapper">
                <label for="selectpickerBasic" class="form-label fw-semibold text-dark">
                  Category
                </label>
                <select class="selectpicker w-100 border-2" id="selectpickerBasic" name="category" data-style="btn-default"
                onchange="changeCategory(this.options[this.selectedIndex].text)">
                  <option value="">Select your business type</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-name="{{ $role->name }}" {{old('category') == $role->id ? 'selected' : ''}}>
                      {{ $role->name }}
                  @endforeach
                </select>
                @error('category')
                  <p class="text-danger">{{ $errors->first('school_subcategory') }}</p>
                @enderror
              </div>


          <!-- School Subcategory -->
          <div class="mb-3 d-none" id="schoolSubcategoryRow">
            <label for="schoolSubcategory" class="form-label fw-semibold text-dark">
              School Category
            </label>
            <div class="w-100 mb-3">
              <select id="selectSchoolCategory" class="select2 form-select" multiple name="school_categories[]">
                  @if($schoolCategory)
                      @foreach($schoolCategory as $school_category)
                          <option value="{{ $school_category->id }}"
                              @if(is_array(old('school_categories')) && in_array($school_category->id, old('school_categories')))
                                  selected
                              @endif
                          >
                              {{ $school_category->name }}
                          </option>
                      @endforeach
                  @endif
              </select>
            </div>
            @error('school_categories')
              <p class="text-danger">{{ $errors->first('school_categories') }}</p>
            @enderror
          </div>
          <div class="mb-3 d-none" id="RestaurantCategory">
            <label for="schoolSubcategory" class="form-label fw-semibold text-dark">
              Type of establishment
            </label>
            <div class="w-100 mb-3">
              <select id="select2Multiple" class="select2 form-select" multiple name="restaurant_categories[]" >
                @if($restaurant_categories)
                  @foreach($restaurant_categories as $restaurant_category)
                      <option value="{{ $restaurant_category->id }}">
                          {{ $restaurant_category->name }}
                      </option>
                  @endforeach
                @endif
              </select>
            </div>

            @error('restaurant_categories')
              <p class="text-danger">{{ $errors->first('restaurant_categories') }}</p>
            @enderror
          </div>

          <div class="mb-3">
            <label for="username" class="form-label">Business Name</label>
            <input type="text" class="form-control" id="business_name" name="business_name" value="{{old('business_name')}}" placeholder="Enter your Business Name"
              autofocus
            >
            <input type="hidden" name="place_id" id="place_id" value="{{old('place_id')}}">
            <div id="place-suggestions" class="list-group shadow suggestion-box"></div>
            @error('business_name')
              <p class="text-danger">{{ $errors->first('business_name') }}</p>
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
            @error('email')
              <p class="text-danger">{{ $errors->first('email') }}</p>
            @enderror
          </div>

          <!-- Password Field -->
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"  />
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
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"   />
                <span class="input-group-text cursor-pointer" onclick="togglePassword('confirm-password', this)">
                  <i class="ti ti-eye-off"></i>
                </span>

              </div>
            </div>

          <div class="mb-3 mt-8">
            <div class="form-check mb-8 ms-2">
              <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" value="accepted">
              <label class="form-check-label" for="terms-conditions">
                I agree to
                <a href="https://www.citio.cool/privacy-policy/" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                &
                <a href="https://www.citio.cool/terms-of-service/" target="_blank" rel="noopener noreferrer">Terms</a>
              </label>
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100 btn-custom">
            Sign up
          </button>

        </form>

        <p class="text-center">
          <span>Already have an account?</span>
          <a href="{{route('business.login')}}">
            <span>Sign in instead</span>
          </a>
        </p>

      </div>
    </div>
    <!-- /Register -->
  </div>
</div>
@endsection
