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
    const $loadingSpinner = $('#vehicle_loading');
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
        // clearTimeout(debounceTimer);
        const isRental = $(this).is(':checked');
        console.log('Loading vehicles for rental status:', isRental);
        
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