@extends('admin.layout')
@section('title', 'Tạo phương tiện')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Tạo phương tiện</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                        
                                <div class="mb-4">
                                    <label class="block text-gray-700">Biển số xe</label>
                                    <input name="plate_number" type="text" placeholder="Biển số xe" value="{{ old('plate_number') }}" required class="form-control p-2 border rounded">
                                    @error('plate_number')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Loại xe</label>
                                    <select name="vehicle_type_id" required class="form-control p-2 border rounded">
                                        <option value="">Chọn loại xe</option>
                                        @foreach($vehicleTypes as $id => $name)
                                            <option value="{{ $id }}" {{ old('vehicle_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_type_id')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Tài xế</label>
                                    <select name="driver_id" class="form-control p-2 border rounded">
                                        <option value="">Chọn tài xế</option>
                                        @foreach($drivers as $id => $name)
                                            <option value="{{ $id }}" {{ old('driver_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Sức chứa</label>
                                    <input name="capacity" type="number" step="0.01" placeholder="Sức chứa" value="{{ old('capacity') }}" class="form-control p-2 border rounded">
                                    @error('capacity')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Năm sản xuất</label>
                                    <input name="manufactured_year" type="number" placeholder="Năm sản xuất" value="{{ old('manufactured_year') }}" class="form-control p-2 border rounded">
                                    @error('manufactured_year')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">Trạng thái</label>
                                    <select name="status" required class="form-control p-2 border rounded">
                                        @foreach($vehicleStatuses as $status => $label)
                                            <option value="{{ $status }}" {{ old('status', 'active') == $status ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Checkbox Xe HPL Thuê -->
                                <div class="form-check form-switch form-switch-lg mb-4">
                                    <input type="checkbox" name="is_car_rental" class="form-check-input" id="isCarRental" value="1" {{ old('is_car_rental') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isCarRental">Xe HPL Thuê</label>
                                    @error('is_car_rental')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Form Khách hàng cho thuê xe (ẩn mặc định) -->
                                <div id="carRentalCustomerForm" style="display: none;">
                                    <hr>
                                    <h6>Thông tin khách hàng cho thuê xe</h6>
                                    <small class="text-muted mb-3 d-block">Bạn có thể chọn 1 khách hàng hoặc tạo 1 khách hàng mới</small>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Chọn khách hàng</label>
                                            <select name="customer_id" class="form-select" id="createCustomerId">
                                                <option value="">Chọn khách hàng</option>
                                                @foreach($carRentalCustomers as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger error" data-field="customer_id"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                            <input name="customer_name" type="text" placeholder="Tên khách hàng" class="form-control" id="createCustomerName">
                                            <div class="text-danger error" data-field="customer_name"></div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                            <input name="customer_phone" type="text" placeholder="Số điện thoại" class="form-control" id="createCustomerPhone">
                                            <div class="text-danger error" data-field="customer_phone"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input name="customer_email" type="email" placeholder="Email" class="form-control" id="createCustomerEmail">
                                            <div class="text-danger error" data-field="customer_email"></div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Địa chỉ</label>
                                            <textarea name="customer_address" placeholder="Địa chỉ" class="form-control" id="createCustomerAddress"></textarea>
                                            <div class="text-danger error" data-field="customer_address"></div>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Tạo phương tiện</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isCarRentalCheckbox = document.getElementById('isCarRental');
    const carRentalCustomerForm = document.getElementById('carRentalCustomerForm');
    
    // Toggle form visibility based on checkbox
    function toggleCarRentalForm() {
        if (isCarRentalCheckbox.checked) {
            carRentalCustomerForm.style.display = 'block';
        } else {
            carRentalCustomerForm.style.display = 'none';
        }
    }
    
    // Initial state
    toggleCarRentalForm();
    
    // Event listener
    isCarRentalCheckbox.addEventListener('change', toggleCarRentalForm);

    // Auto-fill customer data when customer is selected
    const customerSelect = document.querySelector('select[name="customer_id"]');
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const customerId = this.value;
            const customerNameField = document.querySelector('#createCustomerName');
            const customerPhoneField = document.querySelector('#createCustomerPhone');
            const customerEmailField = document.querySelector('#createCustomerEmail');
            const customerAddressField = document.querySelector('#createCustomerAddress');
            
            if (customerId) {
                // Fetch customer data via AJAX
                fetch(`/admin/customers/${customerId}?get_customer_data=1`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Fill customer data into form fields
                    customerNameField.value = data.name || '';
                    customerPhoneField.value = data.phone || '';
                    customerEmailField.value = data.email || '';
                    customerAddressField.value = data.address || '';
                    
                    // Disable input fields when customer is selected
                    customerNameField.disabled = true;
                    customerPhoneField.disabled = true;
                    customerEmailField.disabled = true;
                    customerAddressField.disabled = true;
                    
                    // Clear validation errors
                    clearCustomerErrors();
                })
                .catch(error => {
                    console.error('Error fetching customer data:', error);
                });
            } else {
                // Clear form fields if no customer selected
                customerNameField.value = '';
                customerPhoneField.value = '';
                customerEmailField.value = '';
                customerAddressField.value = '';
                
                // Enable input fields when no customer is selected
                customerNameField.disabled = false;
                customerPhoneField.disabled = false;
                customerEmailField.disabled = false;
                customerAddressField.disabled = false;
            }
        });
    }

    // Frontend validation for create form
    const createForm = document.querySelector('form');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            clearCustomerErrors();
            
            // Validate car rental fields if checkbox is checked
            if (isCarRentalCheckbox.checked) {
                const customerId = document.querySelector('#createCustomerId')?.value;
                const customerName = document.querySelector('#createCustomerName')?.value.trim();
                const customerPhone = document.querySelector('#createCustomerPhone')?.value.trim();
                const customerEmail = document.querySelector('#createCustomerEmail')?.value.trim();
                
                // If no customer selected, validate required fields
                if (!customerId) {
                    if (!customerName) {
                        showCustomerError('customer_name', 'Tên khách hàng là bắt buộc');
                        isValid = false;
                    }
                    
                    if (!customerPhone) {
                        showCustomerError('customer_phone', 'Số điện thoại khách hàng là bắt buộc');
                        isValid = false;
                    }
                    
                    if (!customerEmail) {
                        showCustomerError('customer_email', 'Email khách hàng là bắt buộc');
                        isValid = false;
                    } else if (!isValidEmail(customerEmail)) {
                        showCustomerError('customer_email', 'Email không đúng định dạng');
                        isValid = false;
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Helper functions for validation
    function clearCustomerErrors() {
        const errorElements = document.querySelectorAll('.error[data-field^="customer_"]');
        errorElements.forEach(element => element.textContent = '');
    }
    
    function showCustomerError(field, message) {
        const errorElement = document.querySelector(`.error[data-field="${field}"]`);
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>

@endsection
