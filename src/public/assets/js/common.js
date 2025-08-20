$(document).ready(function() {
    // Format number inputs on change (when user leaves the input)
    $('.number').on('change', function () {
        let value = $(this).val();
        if (value) {
            // Remove all non-numeric characters except dots
            value = value.replace(/[^0-9.]/g, '');
            // Giới hạn tối đa 9 chữ số (không tính dấu chấm thập phân)
            let parts = value.split('.');
            if (parts[0].length > 9) {
                parts[0] = parts[0].substring(0, 9);
                value = parts.join('.');
            }
            if (value && !isNaN(value)) {
                let numericValue = parseFloat(value);
                let formatted = numericValue.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                $(this).val(formatted);
            }
        }
    });

    // Initialize formatting for existing values
    $('.number').each(function() {
        let initial = $(this).val().replace(/[^0-9.]/g, '');
        if (initial) {
            let parts = initial.split('.');
            let formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts[1]) {
                formatted += '.' + parts[1].slice(0, 2);
            }
            $(this).val(formatted);
        }
    });

    // Cache elements
    const $rentalCheckbox = $('#is_car_rental');
    const $vehicleSelect = $('#vehicles');
    const $driversDiv = $('#drivers');
    const $loadingSpinner = $('#vehicle_loading');
    const isRental = $rentalCheckbox.is(':checked');
    if (isRental) {
        $driversDiv.hide();
    } else {
        $driversDiv.show();
    }

    // Store original vehicle value
    let originalVehicleId = $vehicleSelect.val();

    // Alternative implementation with better UX
    function loadVehicles(isRental) {
        const url = "/api/vehicles/by-car-rental";
        console.log(url);
        return $.ajax({
            url: url,
            type: 'GET',
            data: {
                is_car_rental: isRental ? 1 : 0,
                status: 'active' // Optional: only load active vehicles
            },
            dataType: 'json',
            beforeSend: function() {
                $loadingSpinner.show();
                $vehicleSelect.prop('disabled', true);
            }
        });
    }

    // With debounce to prevent multiple rapid requests
    let debounceTimer;
    $rentalCheckbox.on('change', function() {
        clearTimeout(debounceTimer);
        const isRental = $(this).is(':checked');
        if (isRental) {
            $driversDiv.hide();
        } else {
            $driversDiv.show();
        }
        
        debounceTimer = setTimeout(function() {
            loadVehicles(isRental)
                .done(function(response) {
                    updateVehicleOptions(response.vehicles);
                })
                .fail(function(xhr) {
                    handleError(xhr);
                })
                .always(function() {
                    $loadingSpinner.hide();
                    $vehicleSelect.prop('disabled', false);
                });
        }, 300); // 300ms delay
    });

    // Helper function to update vehicle options
    function updateVehicleOptions(vehicles) {
        const currentValue = $vehicleSelect.val();
        
        $vehicleSelect.empty().append('<option value="">Chọn phương tiện</option>');
        
        if (vehicles && vehicles.length > 0) {
            vehicles.forEach(function(vehicle) {
                const optionText = `${vehicle.plate_number} - ${vehicle.vehicle_type}`;
                const $option = $('<option></option>')
                    .attr('value', vehicle.id)
                    .text(optionText);
                
                // Add additional data attributes if needed
                $option.attr('data-capacity', vehicle.capacity);
                $option.attr('data-driver', vehicle.driver_name);
                
                $vehicleSelect.append($option);
            });
            
            // Restore selection if possible
            if (currentValue && $vehicleSelect.find(`option[value="${currentValue}"]`).length) {
                $vehicleSelect.val(currentValue);
            }
        } else {
            $vehicleSelect.append('<option value="" disabled>Không có phương tiện phù hợp</option>');
        }
        
        // Trigger change event if using select2 or similar
        $vehicleSelect.trigger('change');
    }

    // Error handler
    function handleError(xhr) {
        let errorMessage = 'Có lỗi xảy ra khi tải danh sách phương tiện.';
        
        if (xhr.status === 422) {
            errorMessage = 'Dữ liệu không hợp lệ.';
        } else if (xhr.status === 500) {
            errorMessage = 'Lỗi server. Vui lòng thử lại sau.';
        }
        
        // Show error using your notification system
        // Example with toastr:
        // toastr.error(errorMessage);
        
        // Or simple alert
        alert(errorMessage);
    }
});

// Đoạn code jQuery xử lý AJAX
$(document).ready(function() {
    // Xử lý khi thay đổi phương tiện
    $('#vehicles').on('change', function() {
        var vehicleId = $(this).val();
        var isCarRental = $('#is_car_rental').is(':checked');
        
        // Chỉ call AJAX khi KHÔNG chọn "Xe HPL Thuê"
        if (!isCarRental && vehicleId) {
            // Hiển thị loading spinner
            $('#vehicle_loading').show();
            
            // Gọi AJAX để lấy thông tin tài xế
            $.ajax({
                url: '/api/vehicles/get-driver-by-vehicle', // Thay đổi URL theo route của bạn
                type: 'GET',
                data: {
                    vehicle_id: vehicleId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.driver) {
                        // Nếu có tài xế, tự động chọn tài xế đó
                        updateDriverSelection(response.driver);
                    } else {
                        // Reset selection nếu không có tài xế
                        resetDriverSelection();
                    }
                    
                    // Ẩn loading spinner
                    $('#vehicle_loading').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading driver:', error);
                    
                    // Hiển thị thông báo lỗi
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Không thể tải thông tin tài xế');
                    } else {
                        alert('Không thể tải thông tin tài xế');
                    }
                    
                    // Ẩn loading spinner
                    $('#vehicle_loading').hide();
                }
            });
        } else if (isCarRental && vehicleId) {
            // Nếu là xe thuê, reset driver selection
            resetDriverSelection();
            $('#vehicle_loading').hide();
            
            // Có thể hiển thị thông báo
            if (typeof toastr !== 'undefined') {
                toastr.info('Xe thuê - Vui lòng chọn tài xế thủ công');
            }
        } else {
            // Reset nếu không chọn phương tiện
            resetDriverSelection();
            $('#vehicle_loading').hide();
        }
    });
    
    // Xử lý khi thay đổi checkbox "Xe HPL Thuê"
    $('#is_car_rental').on('change', function() {
        var isChecked = $(this).is(':checked');
        var vehicleId = $('#vehicles').val();
        
        // Hiển thị/ẩn phần chi phí xe thuê
        if (isChecked) {
            $('#carRentalCosts').slideDown();
            // Reset driver khi chọn xe thuê
            resetDriverSelection();
        } else {
            $('#carRentalCosts').slideUp();
            // Nếu đã chọn phương tiện, load lại tài xế
            if (vehicleId) {
                $('#vehicles').trigger('change');
            }
        }
    });
    
    // Hàm cập nhật selection tài xế
    function updateDriverSelection(driver) {
        // Tìm select box đầu tiên của tài xế
        var firstDriverSelect = $('select[name="drivers[0][user_id]"]');
        
        if (firstDriverSelect.length > 0) {
            // Set giá trị cho select box
            firstDriverSelect.val(driver.id);
            
            // Nếu select2 được sử dụng
            if (firstDriverSelect.hasClass('select2') || firstDriverSelect.data('select2')) {
                firstDriverSelect.trigger('change.select2');
            }
            
            // Tự động check "Lái chính" cho tài xế này
            $('input[name="drivers[0][deductions][is_main_driver]"]').prop('checked', true);
            
            // Hiển thị thông tin tài xế
            showDriverInfo(driver);
        } else {
            // Nếu chưa có row tài xế, tự động thêm và chọn
            addDriverRowWithData(driver);
        }
    }
    
    // Hàm reset selection
    function resetDriverSelection() {
        // Reset select box đầu tiên
        var firstDriverSelect = $('select[name="drivers[0][user_id]"]');
        firstDriverSelect.val('');
        
        // Trigger change event cho select2
        if (firstDriverSelect.hasClass('select2') || firstDriverSelect.data('select2')) {
            firstDriverSelect.trigger('change.select2');
        }
        
        // Uncheck "Lái chính"
        $('input[name="drivers[0][deductions][is_main_driver]"]').prop('checked', false);
        
        // Clear các input khác trong row đầu tiên
        $('#personTable tbody tr:first-child input[type="text"]').val('');
        $('#personTable tbody tr:first-child input[type="number"]').val('');
    }
    
    // Hàm hiển thị thông tin tài xế
    function showDriverInfo(driver) {
        var message = 'Đã tự động chọn tài xế: ' + driver.full_name;
        
        if (driver.phone) {
            message += ' - SĐT: ' + driver.phone;
        }
        if (driver.employee_code) {
            message += ' - Mã NV: ' + driver.employee_code;
        }
        
        // Sử dụng toastr nếu có, nếu không dùng console.log
        if (typeof toastr !== 'undefined') {
            toastr.success(message, 'Thông báo', {
                timeOut: 3000,
                positionClass: 'toast-top-right'
            });
        } else {
            console.log(message);
        }
    }
    
    // Hàm thêm row tài xế với data (dùng khi chưa có row nào)
    function addDriverRowWithData(driver) {
        var rowCount = $('#personTable tbody tr').length;
        var optionsHtml = '';
        
        // Clone options từ một select có sẵn hoặc tạo mới
        var existingSelect = $('select[name^="drivers"][name$="[user_id]"]:first');
        if (existingSelect.length > 0) {
            existingSelect.find('option').each(function() {
                var selected = ($(this).val() == driver.id) ? 'selected' : '';
                optionsHtml += `<option value="${$(this).val()}" ${selected}>${$(this).text()}</option>`;
            });
        }
        
        var newRow = `
            <tr>
                <td>
                    <select name="drivers[${rowCount}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block">
                        <input type="checkbox" name="drivers[${rowCount}][deductions][is_main_driver]" 
                               class="form-check-input deduction-input" value="1" checked>
                    </div>
                </td>`;
        
        // Thêm các cột cho deduction types (lấy từ header)
        $('#personTable thead th').each(function(index) {
            // Bỏ qua cột đầu (Nhân sự), cột Lái chính, cột Ghi chú và cột action
            if (index > 1 && index < $('#personTable thead th').length - 2) {
                newRow += `
                    <td>
                        <input type="text" name="drivers[${rowCount}][deductions][type_${index}]" 
                               class="form-control form-control-sm deduction-input" min="0">
                    </td>`;
            }
        });
        
        newRow += `
                <td>
                    <input type="text" name="drivers[${rowCount}][deductions][Ghi chú]" 
                           class="form-control form-control-sm">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="removeDriverRow(this, ${rowCount})">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                    <input type="hidden" name="driver_rows[]" value="${rowCount}">
                </td>
            </tr>`;
        
        $('#personTable tbody').append(newRow);
        
        // Init select2 nếu cần
        if ($.fn.select2) {
            $(`select[name="drivers[${rowCount}][user_id]"]`).select2();
        }
    }
    
    // Trigger change event khi load page nếu đã có vehicle được chọn
    if ($('#vehicles').val() && !$('#is_car_rental').is(':checked')) {
        $('#vehicles').trigger('change');
    }
});