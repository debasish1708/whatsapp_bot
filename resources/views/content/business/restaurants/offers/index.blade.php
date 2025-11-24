@extends('layouts.layoutMaster')

@section('title', __('Offers'))

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
  @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss', 'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
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

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
  @vite(['resources/assets/vendor/libs/toastr/toastr.js'])
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
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

@section('page-style')
  <style>
    .dataTables_filter input {
    height: 35px !important;
    }

    /* Ensure Select2 dropdowns are properly styled */
    .select2-container--default .select2-selection--single {
      height: 38px;
      border: 1px solid #d9dee3;
      border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 36px;
      padding-left: 0.75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px;
    }

    .select2-dropdown {
      border: 1px solid #d9dee3;
      border-radius: 0.375rem;
      box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
    }
    .cursor-pointer {
        cursor: pointer;
    }

    .cursor-pointer:hover {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.2s ease;
    }
  </style>

@endsection
@section('page-script')
@vite(['resources/assets/js/forms-selects.js', 'resources/assets/js/forms-tagify.js', 'resources/assets/js/forms-typeahead.js'])
<script>
    // Function to show all items in a modal
    function showAllItems(element) {
        const items = JSON.parse(element.getAttribute('data-items'));
        const offerTitle = element.getAttribute('data-offer-title');

        let itemsHtml = items.map(item =>
            `<span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">${item}</span>`
        ).join('');

        // let itemsHtml = items.map((item, index) => {
        //     const text = (item && item.name) ? item.name : item;
        //     const badge = `<span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">${text}</span>`;
        //     // insert a full-width break before every 6th item to force a new row
        //     return (index > 0 && index % 6 === 0) ? `<div class="w-100"></div>${badge}` : badge;
        // }).join('');

        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="itemsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${offerTitle} - {{ __('All Items') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex flex-wrap gap-2">
                                ${itemsHtml}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#itemsModal').remove();

        // Append and show modal
        $('body').append(modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('itemsModal'));
        modal.show();

        // Clean up modal after it's hidden
        $('#itemsModal').on('hidden.bs.modal', function () {
            $(this).remove();
        });
    }
</script>
@if (isset($is_visited) && $is_visited == false)
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
          title: "{{__('restaurant_offers.tour.table_title')}}",
          text: "{{__('restaurant_offers.tour.table_text')}}",
          element: '.card-datatable',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_offers.tour.search_title')}}",
          text: "{{__('restaurant_offers.tour.search_text')}}",
          element: '.dataTables_filter input',
          position: 'bottom',
          skipText: 'Skip',
          nextText: 'Next'
        },
        {
          title: "{{__('restaurant_offers.tour.add_title')}}",
          text: "{{__('restaurant_offers.tour.add_text')}}",
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var flatpickrDate = document.querySelector('#flatpickr-date');
        var flatpickrDate1 = document.querySelector('#flatpickr-date1');
        var date = document.querySelector('.date');
        var date1 = document.querySelector('.date1');
        if (flatpickrDate) {
            flatpickrDate.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today', // Set minimum date to today
                onChange: function(selectedDates, dateStr) {
                    // Update the minimum date of the end date picker
                    if (flatpickrDate1._flatpickr) {
                        flatpickrDate1._flatpickr.set('minDate', dateStr);
                    }
                }
            });
        }
        if (date) {
            date.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today',
                onChange: function(selectedDates, dateStr) {
                    // Update the minimum date of the end date picker
                    if (date1._flatpickr) {
                        date1._flatpickr.set('minDate', dateStr);
                    }
                }
            });
        }
        if (flatpickrDate1) {
            flatpickrDate1.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today' // Initial minimum date
            });
        }
        if (date1) {
            date1.flatpickr({
                monthSelectorType: 'static',
                minDate: 'today' // Initial minimum date
            });
        }

        // Initialize Select2 for discount type selects and handle placeholder changes
        if ($('#discount_type').length) {
            $('#discount_type').select2({
                placeholder: "{{ __('Select discount type') }}",
                dropdownParent: $('#discount_type').parent()
            }).on('select2:select', function(e) {
                const selectedValue = e.params.data.id;
                const discountInput = document.getElementById('discount');
                if (discountInput) {
                    if (selectedValue === 'fixed') {
                        discountInput.placeholder = "{{ __('Enter Amount') }}";
                    } else if (selectedValue === 'percentage') {
                        discountInput.placeholder = "{{ __('Enter Percentage') }}";
                    } else {
                        discountInput.placeholder = "{{ __('Select discount type first') }}";
                    }
                }
            }).on('select2:unselect', function(e) {
                const discountInput = document.getElementById('discount');
                if (discountInput) {
                    discountInput.placeholder = "{{ __('Select discount type first') }}";
                }
            });
        }

        if ($('#discount_type_edit').length) {
            $('#discount_type_edit').select2({
                placeholder: 'Select discount type',
                dropdownParent: $('#discount_type_edit').parent()
            }).on('select2:select', function(e) {
                const selectedValue = e.params.data.id;
                const discountEditInput = document.getElementById('discount-edit');
                if (discountEditInput) {
                    if (selectedValue === 'fixed') {
                        discountEditInput.placeholder = "{{ __('Enter Amount') }}";
                    } else if (selectedValue === 'percentage') {
                        discountEditInput.placeholder = "{{ __('Enter Percentage') }}";
                    } else {
                        discountEditInput.placeholder = "{{ __('Select discount type first') }}";
                    }
                }
            }).on('select2:unselect', function(e) {
                const discountEditInput = document.getElementById('discount-edit');
                if (discountEditInput) {
                    discountEditInput.placeholder = "{{ __('Select discount type first') }}";
                }
            });
        }

        // Function to update placeholder based on current selection
        function updateDiscountPlaceholder() {
            const discountType = $('#discount_type').val();
            const discountInput = document.getElementById('discount');
            if (discountInput && discountType) {
                if (discountType === 'fixed') {
                    discountInput.placeholder = "{{ __('Enter Amount') }}";
                } else if (discountType === 'percentage') {
                    discountInput.placeholder = "{{ __('Enter Percentage') }}";
                } else {
                    discountInput.placeholder = "{{ __('Select discount type first') }}";
                }
            }
        }

        // Check initial value and update placeholder
        setTimeout(function() {
            updateDiscountPlaceholder();
        }, 100);

        // Initialize the datatable
        // Replace your existing DataTable initialization with this updated version
        var table = $('.datatables-users').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '{{ route('restaurant.offers.index') }}',
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID', width: '5%' },
                { data: 'title', width: '14%' },
                { data: 'description', width: '20%' },
                { data: 'discount_type', width: '10%' },
                { data: 'discount', width: '8%' },
                { data: 'starts_from', width: '10%' },
                { data: 'ends_at', width: '10%' },
                { data: 'Items', name: 'Items', orderable: false, searchable: false, width: '8%' },
                { data: 'status', name: 'status', orderable: false, searchable: false, width: '8%' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '7%' },
            ],
            columnDefs: [
                {
                    targets: [1, 2, 7], // title, description, and Items columns
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            return '<div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: normal;">' + data + '</div>';
                        }
                        return data;
                    }
                }
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
                text: '<i class="ti ti-plus me-1"></i> {{ __("Add Offer") }}',
                className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
                action: function (e, dt, node, config) {
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
        $('.dataTables_filter input').attr('placeholder', '{{__('Search offers...')}}');
    });

    function clearAddFormFields(){
        $('.add-error').text('');
        $('.add-field').val('');

        // Clear Select2 dropdowns
        $('#discount_type').val(null).trigger('change');
        $('#selectpickerBasic').val('').trigger('change');

        // Clear date pickers
        $('#flatpickr-date').val('');
        $('#flatpickr-date1').val('');
        if ($('#flatpickr-date')[0]._flatpickr) {
            $('#flatpickr-date')[0]._flatpickr.clear();
        }
        if ($('#flatpickr-date1')[0]._flatpickr) {
            $('#flatpickr-date1')[0]._flatpickr.clear();
        }

        // Clear discount field
        $('#discount').val('');

        // Reset discount placeholder
        const discountInput = document.getElementById('discount');
        if (discountInput) {
            discountInput.placeholder = "{{ __('Select discount type first') }}";
        }
    }

    function clearEditFormFields(){
        $('.edit-error').text('');
        $('.edit-input').val('');
        // Reset discount placeholder
        const discountEditInput = document.getElementById('discount-edit');
        if (discountEditInput) {
            discountEditInput.placeholder = "{{ __('Select discount type first') }}";
        }
    }

    function handleEditMenuItem(data) {
        clearEditFormFields();
        setEditDataToForm(data);
    }

    function handleEditOffer(id) {
        clearEditFormFields();
        $.ajax({
            url: '{{ route("restaurant.offers.edit", ":id") }}'.replace(':id', id),
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res && res.data) {
                    handleEditMenuItem(res.data);
                } else {
                    toastr.error('Failed to load offer data.', 'Error');
                }
            },
            error: function (xhr) {
                toastr.error('Failed to load offer data.', 'Error');
                console.error(xhr);
            }
        });
    }

    function setEditDataToForm(data){
        $("#item-id").val(data.id);
        const selectedIds = (data.applicable_items || []).map(function (item) { return String(item.id); });
        const selectEl = $('#selectPickerEdit');
        selectEl.select2({
            dropdownParent: $('#offcanvasEditUser'),
            width: '100%'
        });
        const existingValues = selectEl.find('option').map(function(){ return String(this.value); }).get();
        (data.applicable_items || []).forEach(function(item){
            if (!existingValues.includes(String(item.id))) {
                const option = new Option(item.name || ('Item ' + item.id), String(item.id), false, false);
                selectEl.append(option);
            }
        });
        selectEl.val(selectedIds).trigger('change');
        $("#offer-title-edit").val(data.title);
        $("#offer-description-edit").val(data.description);
        $("#item-price-edit").val(data.price);
        // $("#discount_type_edit").val(data.discount_type);
        $('#discount_type_edit').select2({
            dropdownParent: $('#offcanvasEditUser'),
            placeholder: '{{ __("Select type") }}',
            allowClear: true,
            width: '100%'
          }).val(data.discount_type).trigger('change');

        $("#discount-edit").val(data.discount);
        $('.date').val(data.starts_from);
        $('.date1').val(data.ends_at);

        // Set discount type and update placeholder
        const discountEditType = document.getElementById('discount_type_edit');
        const discountEditInput = document.getElementById('discount-edit');
        if (discountEditType && discountEditInput) {
            // Set the discount type based on data
            if (data.discount_percentage) {
                $('#discount_type_edit').val('percentage').trigger('change');
                discountEditInput.placeholder = 'Enter Percentage';
                discountEditInput.value = data.discount_percentage;
            } else if (data.discount_amount) {
                $('#discount_type_edit').val('fixed').trigger('change');
                discountEditInput.placeholder = 'Enter Amount';
                discountEditInput.value = data.discount_amount;
            }
        }

        // $("#edit-menu-item-form").attr('action', "{{ route('menu-items.update', ':id') }}".replace(':id', data.id));

        const form = document.getElementById('edit-menu-item-form');
        const actionTemplate = form.getAttribute('data-action-template');
        const updatedAction = actionTemplate.replace(':id', data.id);
        form.setAttribute('action', updatedAction);

        $('#offcanvasEditUser').offcanvas('show');
    }

    function handleViewOffer(id) {
        $.ajax({
            url: '{{ route("restaurant.offers.show", ":id") }}'.replace(':id', id),
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                let data = res.data;
                const date = new Date(data.created_at);
                const formatted = date.getFullYear() + '-' +
                    String(date.getMonth() + 1).padStart(2, '0') + '-' +
                    String(date.getDate()).padStart(2, '0') + ' ' +
                    String(date.getHours()).padStart(2, '0') + ':' +
                    String(date.getMinutes()).padStart(2, '0');

                const selectedNames = data.applicable_items.map(item => item.name).join(', ');

                $('#modal-title').text(data.title || '');
                $('#modal-description').text(data.description || '');
                $('#modal-starts-from').text(data.starts_from || '');
                $('#modal-ends-at').text(data.ends_at || '');
                $('#modal-discount').text(data.discount_percentage+' %' || data.discount_amount+' CZK');
                $('#created').text(formatted || '');
                $("#modal-applicable-items").text(selectedNames);
                $("#modalCenter").modal('show');
            },
            error: function (error) {
                console.error('Error:', error);
                toastr.error('Failed to fetch offer details.', 'Error');
            }
        });
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
                        console.log(result);
                        refreshApplicableItems(result.restaurantId);
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message, 'Error');
                        console.log(error);
                    }
                })
            }
        })
    }

    function refreshApplicableItems(restaurantId) {
        $.ajax({
            url: `/restaurant/${restaurantId}/available-items`,
            type: 'GET',
            success: function (data) {
                const select = $('#selectpickerBasic');
                select.empty(); // clear existing options

                data.forEach(item => {
                    select.append(`<option id="item_${item.id}" value="${item.id}">${item.name}</option>`);
                });

                select.trigger('change'); // refresh Select2 UI
            },
            error: function () {
                alert('Failed to refresh items.');
            }
        });
    }

    // Example usage: after deleting an offer
    // $(document).on('click', '.delete-offer', function () {
    //     const offerId = $(this).data('id');
    //     const restaurantId = $(this).data('restaurant-id');

    //     $.ajax({
    //         url: `/offers/${offerId}`,
    //         type: 'DELETE',
    //         data: { _token: '{{ csrf_token() }}' },
    //         success: function () {
    //             refreshApplicableItems(restaurantId); // 🔁 refresh available items
    //         }
    //     });
    // });
</script>
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
                    let id="{{session()->get('offer')}}";
                    let url = "{{route('restaurant.offers.edit',':id')}}".replace(':id',id);
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
<h4>{{__('Offers')}}</h4>
<div class="card">
    <div class="card-datatable table-responsive">
        <table class="datatables-users table">
            <thead class="border-top">
                <tr>
                    <th>ID</th>
                    <th>{{__('title')}}</th>
                    <th>{{__('description')}}</th>
                    <th>{{__('discount type')}}</th>
                    <th>{{__('discount')}}</th>
                    <th>{{__('starts from')}}</th>
                    <th>{{__('ends at')}}</th>
                    <th>{{__('Items')}}</th>
                    <th>{{__('status')}}</th>
                    <th>{{__(key: 'actions')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Offcanvas to add new user -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Add Offer')}}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('restaurant.offers.store') }}" method="post">
            @csrf
            <div class="mb-6">
                <label class="form-label" for="offer-title">{{__('Title')}}</label>
                <input type="text" class="form-control add-field" id="offer-title" placeholder="{{__('Enter offer title')}}" name="title" required
                    value="{{old('title')}}" />
                <small class="text-danger add-error">{{ $errors->first('title') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="offer-description">{{__('description')}}</label>
                <textarea class="form-control add-field" id="offer-description" placeholder="{{__('Enter offer description')}}"
                    name="description" rows="3" required>{{old('description')}}</textarea>
                <small class="text-danger add-error">{{ $errors->first('description') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="discount_type">{{ __('Discount Type') }}</label>
                <select id="discount_type" class="select2 form-select @error('discount_type') is-invalid @enderror" name="discount_type" required>
                    <option value="">{{ __('Select Value') }}</option>
                    @foreach (\App\Enums\RestaurantOfferType::cases() as $type)
                        <option value="{{ $type->value }}" {{ old('discount_type') == $type->value ? 'selected' : '' }}>
                            {{ __($type->label()) }}
                        </option>
                    @endforeach
                </select>
                @error('discount_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label class="form-label" for="discount">{{ __('Discount') }}</label>
                <input type="number"
                    class="form-control add-field"
                    id="discount"
                    name="discount"
                    placeholder="{{ __('Select discount type first') }}"
                    value="{{ old('discount') }}"
                    min="0"
                    required />
                <small class="text-danger add-error">{{ $errors->first('discount') }}</small>
            </div>

            <div class="mb-6">
                <label class="form-label" for="flatpickr-date">{{__('Starts from')}}</label>
                <input type="text" class="form-control" name="starts_from" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
                <small class="text-danger">{{ $errors->first('starts_from') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="flatpickr-date1">{{__('Ends at')}}</label>
                <input type="text" class="form-control" name="ends_at" placeholder="YYYY-MM-DD" id="flatpickr-date1" required />
                <small class="text-danger">{{ $errors->first('ends_at') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="selectpickerBasic">{{__('Applicable items')}}</label>
                <select id="selectpickerBasic" class="select2 form-select" multiple data-style="btn-default" name="applicable_items[]" required>
                    {{-- <option value="">{{__('Select applicable items')}}</option> --}}
                    @foreach ($menu_items as $menu_item)
                        <option id="{{'item_' . $menu_item->id}}" value="{{ $menu_item->id }}">{{ $menu_item->name }}</option>
                    @endforeach
                </select>
                <small class="text-danger add-error">{{ $errors->first('applicable_items') }}</small>
            </div>
            <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">{{__('Add Offer')}}</button>
            {{-- <button type="reset" id="cancelButton" class="btn btn-label-danger"
            data-bs-dismiss="offcanvas">Cancel</button> --}}
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">{{__('Edit Offer')}}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="add-new-user pt-0" id="edit-menu-item-form" data-action-template="{{route('restaurant.offers.update', ':id')}}" method="post">
            @csrf
            @method('put')
            <div class="mb-6">
                <label class="form-label" for="offer-title-edit">{{__('Title')}}</label>
                <input type="text" class="form-control add-field" id="offer-title-edit" placeholder="{{__('Enter offer title')}}" name="title" required
                />
                <small class="text-danger edit-error">{{ $errors->first('title') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="offer-description-edit">{{__('description')}}</label>
                <textarea class="form-control add-field" id="offer-description-edit" placeholder="{{__('Enter offer description')}}"
                    name="description" rows="3" required></textarea>
                <small class="text-danger edit-error">{{ $errors->first('description') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="discount_type_edit">{{ __('Discount Type') }}</label>
                <select id="discount_type_edit" class="select2 form-select @error('discount_type') is-invalid @enderror" name="discount_type" required>
                    <option value="">{{ __('Select type') }}</option>
                    @foreach (\App\Enums\RestaurantOfferType::cases() as $type)
                         <option value="{{ $type->value }}" {{ old('discount_type' || $data['discount_type'] ) == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('discount_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label class="form-label" for="discount-edit">{{__('Discount')}}</label>
                <input type="number" class="form-control edit-input" id="discount-edit" placeholder="{{ __('Select discount type first') }}" name="discount" />
                <small class="text-danger edit-error">{{ $errors->first('discount_amount') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="flatpickr-date">{{__('Starts from')}}</label>
                <input type="text" class="form-control date" name="starts_from" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
                <small class="text-danger edit-error">{{ $errors->first('starts_from') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="flatpickr-date1">{{__('Ends at')}}</label>
                <input type="text" class="form-control date1" name="ends_at" placeholder="YYYY-MM-DD" id="flatpickr-date1" required />
                <small class="text-danger edit-error">{{ $errors->first('ends_at') }}</small>
            </div>
            <div class="mb-6">
                <label class="form-label" for="selectpickerBasic">{{__('Applicable items')}}</label>
                <select id="selectPickerEdit" class="select2 form-select" multiple data-style="btn-default" name="applicable_items[]" required>
                    @foreach ($menu_items as $menu_item)
                        <option id="{{'item_' . $menu_item->id}}" value="{{ $menu_item->id }}">{{ $menu_item->name }}</option>
                    @endforeach
                </select>
                <small class="text-danger edit-error">{{ $errors->first('applicable_items') }}</small>
            </div>
            <button type="submit" id="updateLavel" class="btn btn-primary me-3 data-submit btn-custom">{{__('Edit Offer')}}</button>
            {{-- <button type="reset" id="cancelButton" class="btn btn-label-danger"
            data-bs-dismiss="offcanvas">Cancel</button> --}}
        </form>
    </div>
</div>

<div class="col-lg-4 col-md-6">
    <div class="mt-4">
        <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="modalCenterTitle">{{__('Offer details')}}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <ul class="list-unstyled">
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-bell ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('title')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-title"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-align-left ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('description')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-description"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Starts from')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-starts-from"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar-event ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Ends at')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-ends-at"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-tag ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Discount percentage/price')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-discount"></span>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-tag ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Applicable items')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="modal-applicable-items"></span>
                              </div>
                          </li>
                          <li class="mb-2 pt-1">
                              <div class="d-flex">
                                  <div class="d-flex h-100">
                                      <div class="badge bg-label-primary p-1 m-1 rounded">
                                          <i class='ti ti-calendar ti-sm'></i>
                                      </div>
                                      <span class="fw-semibold me-1 m-2">{{__('Added on')}}:</span>
                                  </div>
                                  <span class="text-break m-2" id="created"></span>
                              </div>
                          </li>
                      </ul>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
