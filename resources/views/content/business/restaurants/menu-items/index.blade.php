@extends('layouts.layoutMaster')

@section('title', __('Menu Items'))

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

@section('page-style')
  <style>
    .dataTables_filter input {
    height: 35px !important;
    }
  </style>

@endsection
@section('page-script')
  @vite(['resources/assets/js/forms-selects.js', 'resources/assets/js/forms-tagify.js', 'resources/assets/js/forms-typeahead.js'])
  <script>
    document.addEventListener('DOMContentLoaded', function () {

    // Initialize the datatable
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('menu-items.index') }}',
      columns: [
      { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
      { data: 'name' },
      { data: 'description' },
      { data: 'price' },
      { data: 'tags' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
      ],
      dom: '<"row"' +
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
      buttons: [{
      text: '<i class="ti ti-plus me-1"></i> {{ __('Add Item') }}',
      className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
      action: function (e, dt, node, config) {
        // You can trigger your add post modal or logic here
        clearAddFormFields();
        $('#offcanvasAddUser').offcanvas('show');
      }
      }],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
      language: {
          "emptyTable": "{{ __('No data available in table') }}",
          "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
          "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
          "infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
          "lengthMenu": "{{ __('Show _MENU_ entries') }}",
          "loadingRecords": "{{ __('Loading...') }}",
          "processing": "{{ __('Processing...') }}",
          "search": "{{ __('Search:') }}",
          "zeroRecords": "{{ __('No matching records found') }}",
          "paginate": {
            "first": "{{ __('First') }}",
            "last": "{{ __('Last') }}",
            "next": "{{ __('Next') }}",
            "previous": "{{ __('Previous') }}"
          }
      }
    });

    // Optional: Adjust search input placeholder for clarity
    $('.dataTables_filter input').attr('placeholder', '{{__('Search menu items...')}}');
    });

    function clearAddFormFields(){
      $('.add-error').text('');
      $('.add-field').val('');
    }

    function clearEditFormFields(){
      $('.edit-error').text('');
      $('.edit-input').val('');
    }

    function handleEditMenuItem(data) {
      clearEditFormFields();
      //let categories = {!! json_encode($restaurant_categories) !!};
      setEditDataToForm(data);
    }

    function setEditDataToForm(data){
      $("#item-id").val(data.id);
      $('#selectpickerBasic').selectpicker('val',data.menu_category_id);
      $("#item-name-edit").val(data.name);
      $("#item-description-edit").val(data.description);
      $("#item-price-edit").val(data.price);

      const input = document.querySelector('#item-tags-edit');
      let tagify = input._tagify;
      if (tagify) {
        tagify.removeAllTags();
      } else {
        tagify = new Tagify(input);
        tagify.removeAllTags();
      }
      tagify.addTags(JSON.parse(data.tags));

      // $("#edit-menu-item-form").attr('action', "{{ route('menu-items.update', ':id') }}".replace(':id', data.id));

      const form = document.getElementById('edit-menu-item-form');
      const actionTemplate = form.getAttribute('data-action-template');
      const updatedAction = actionTemplate.replace(':id', data.id);
      form.setAttribute('action', updatedAction);

      $('#offcanvasEditUser').offcanvas('show');
    }

    function deleteItem(url){
      // let url=$(this).data('url');
      Swal.fire({
        text: "{{__('Are you sure?')}}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: {
          confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          // let url="{{--route('receipts.reject', ['receipt'=>":receipt"])--}}".replace(":receipt",id);
          $.ajax({
            method: 'DELETE',
            url: url,
            data: {
              _token: "{{ csrf_token() }}"
            },
            success: function (result) {
              toastr.success(result.message, "Success");
              $('.datatables-users').DataTable().ajax.reload(null, false);
            },
            error: function (error) {
              toastr.error(error.responseJSON.message, 'Error');
              console.log(error);
            }
          })
        }
      })
    }
  </script>
@if (isset($is_already_visited) && $is_already_visited == false)
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const tour = new Shepherd.Tour({
        defaultStepOptions: {
          scrollTo: false,
          cancelIcon: { enabled: true }
        },
        useModalOverlay: true
      });
      const stepsInput = [
        {
          title: "{{__('restaurant_menu_items.tour.table_title')}}",
          text: "{{__('restaurant_menu_items.tour.table_text')}}",
          element: '.card-datatable',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_menu_items.tour.search_title')}}",
          text: "{{__('restaurant_menu_items.tour.search_text')}}",
          element: '.dataTables_filter input',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_menu_items.tour.add_title')}}",
          text: "{{__('restaurant_menu_items.tour.add_text')}}",
          element: '.create-post-btn',
          position: 'bottom',
          nextText: 'Continue',
          finishText: 'Finish'
        }
      ];
      const steps = typeof generateTourSteps === 'function' ? generateTourSteps(stepsInput) : stepsInput;
      steps.forEach(step => tour.addStep(step));
      tour.start();
    });
  </script>
@endif
@endsection

@section('content')
  @if($errors->any() && session()->exists('offcanvas'))
    @if(session()->get('offcanvas') == 'add')
      <script>
        document.addEventListener('DOMContentLoaded', function () {

          $('#offcanvasAddUser').offcanvas('show');
        })
      </script>
    @endif
    @if(session()->get('offcanvas') == 'edit')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          let id="{{session()->get('menu_item')}}";
          let url = "{{route('menu-items.edit',':id')}}".replace(':id',id);
          $.ajax({
            url:url,
            method:'get',
            success:function(data){
              console.log(data.data);
              document.querySelectorAll('.add-error').textContent = '';
              setEditDataToForm(data.data);
            },
            error:function(error){
              console.log(error);
            }
          });
          //$('#offcanvasEditUser').offcanvas('show');
        })
      </script>
    @endif
  @endif
  <h4>{{__('Menu Items')}}</h4>
  <div class="card">
    <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
      <tr>
        <th>ID</th>
        <th>{{__('name')}}</th>
        <th>{{__('description')}}</th>
        <th>{{__('price')}}</th>
        <th>{{__('tags')}}</th>
        {{-- <th>Status</th>
        <th>Type</th> --}}
        <th>{{__(key: 'actions')}}</th>
      </tr>
      </thead>
    </table>
    </div>
  </div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Add Item')}}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
    <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('menu-items.store') }}" method="post"
      enctype="multipart/form-data">
      @csrf
      <div class="mb-6">
      <label class="form-label" for="addSelectpickerBasic">{{__('category')}}</label>
      <select id="addSelectpickerBasic" class="selectpicker w-100" name="category_id" data-style="btn-default"
        required>
        <option value="">{{__('Select item category')}}</option>
        @foreach ($restaurant_categories as $category)
      <option value="{{ $category->id }}">{{ __($category->name) }}</option>
      @endforeach
      </select>
      <small class="text-danger add-error">{{ $errors->first('category_id') }}</small>
      </div>
      <input type="hidden" name="announcement_id" id="announcement-id" value="">
      <div class="mb-6">
      <label class="form-label" for="item-name">{{__('name')}}</label>
      <input type="text" class="form-control add-field" id="item-name" placeholder="{{__('Enter Item name')}}" name="name" required
        value="{{old('name')}}" />
      <small class="text-danger add-error">{{ $errors->first('name') }}</small>
      </div>
      <div class="mb-6">
      <label class="form-label" for="announcement-description">{{__('description')}}</label>
      <textarea class="form-control add-field" id="announcement-description" placeholder="{{__('Enter item description')}}"
        name="description" rows="3" required>{{old('description')}}</textarea>
      <small class="text-danger add-error">{{ $errors->first('description') }}</small>
      </div>
      <div class="mb-6">
      <label class="form-label" for="flatpickr-date">{{__('price')}}</label>
      <input type="text" class="form-control add-field" id="item-price" placeholder="{{__('Enter Item price')}}" name="price" required
        value="{{old('price')}}" />
      <small class="text-danger add-error">{{ $errors->first('price') }}</small>
      </div>
      <div class="mb-6">
      <label class="form-label" for="flatpickr-date">{{__('Item images')}}</label>
      <input type="file" class="form-control add-field" id="item-images" name="images[]" multiple accept="image/*" />
      <small class="text-danger add-error">{{ $errors->first('images') }}</small>
      </div>
      <div class="mb-6">
      <label class="form-label" for="TagifyBasic">{{__('tags')}}</label>
      <input id="TagifyBasic" class="form-control add-field" name="tags" value="{{old('tags')}}" />
      <small class="text-danger add-error">{{ $errors->first('tags') }}</small>
      </div>
      <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">{{__('Add Item')}}</button>
      {{-- <button type="reset" id="cancelButton" class="btn btn-label-danger"
      data-bs-dismiss="offcanvas">Cancel</button> --}}
    </form>
    </div>
  </div>
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
      <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Edit Item')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
      <form class="add-new-user pt-0" id="edit-menu-item-form" data-action-template="{{route('menu-items.update', ':id')}}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="mb-6">
          <label class="form-label" for="announcement-status">{{__('category')}}</label>
          <select id="selectpickerBasic" class="selectpicker w-100 item-category" data-style="btn-default" name="category_id" required>
            <option value="">{{__('Select item category')}}</option>
            @foreach ($restaurant_categories as $category)
              <option id="{{'category_' . $category->id}}" value="{{ $category->id }}">{{ __($category->name) }}</option>
            @endforeach
          </select>
          <small class="text-danger edit-error">{{ $errors->first('category_id') }}</small>
        </div>
        <input type="hidden" name="item_id" id="item-id" value="">
        <div class="mb-6">
          <label class="form-label" for="item-name-edit">{{__('name')}}</label>
          <input type="text" class="form-control edit-input" id="item-name-edit" placeholder="{{__('Enter Item name')}}" name="name" required />
          <small class="text-danger edit-error">{{ $errors->first('name') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="item-description-edit">{{__('description')}}</label>
          <textarea class="form-control edit-input" id="item-description-edit" placeholder="{{__('Enter item description')}}" name="description" rows="3" required></textarea>
          <small class="text-danger edit-error">{{ $errors->first('description') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="flatpickr-date">{{__('price')}}</label>
          <input type="text" class="form-control edit-input" id="item-price-edit" placeholder="{{__('Enter Item price')}}" name="price" required />
          <small class="text-danger edit-error">{{ $errors->first('price') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="flatpickr-date">{{__('Item images')}}</label>
          <input type="file" class="form-control edit-input" id="item-images" name="images[]" multiple accept="image/*" />
          <small class="text-danger edit-error">{{ $errors->first('images') }}</small>
        </div>
        <div class="mb-6">
          <label class="form-label" for="TagifyBasic">{{__('tags')}}</label>
          <input id="item-tags-edit" class="form-control edit-input" name="tags" />
          <small class="text-danger edit-error">{{ $errors->first('tags') }}</small>
        </div>
        <button type="submit" id="updateItem" class="btn btn-primary me-3 data-submit btn-custom">{{__('Edit Item')}}</button>
      </form>
    </div>
  </div>
@endsection
