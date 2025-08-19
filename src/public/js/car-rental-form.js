/**
 * Car Rental Form JavaScript
 * Xử lý form tạo nhật ký lộ trình cho car-rental theo yêu cầu Issue #180
 */

// Khai báo các biến cần thiết
const goodsTable = document.querySelector('#goodsTable tbody');
let goodsCount = 0;
const personTable = document.querySelector('#personTable tbody');
const personPxTable = document.querySelector('#personPxTable tbody');

// Lưu trữ dữ liệu cũ từ validation errors
window.laravelOld = window.laravelOld || {};

// Khai báo các loại khấu trừ cho tài xế - sẽ được set từ Blade template
let personDeductionTypes = [];
let personPxDeductionTypes = [];

// Gán danh sách người dùng vào biến toàn cục - sẽ được set từ Blade template
window.users = {};
window.userPXs = {};
window.vehicles = []; // Khởi tạo mảng rỗng cho vehicles

// Biến để theo dõi số lượng hàng driver đã thêm vào
let driverRowCount = 0;

// Hàm cập nhật danh sách xe
function updateVehicleList(showOnlyRental = false) {
    const vehiclesSelect = document.getElementById('vehicles');
    if (!vehiclesSelect) return;
    
    // Lấy xe hiện tại được chọn từ HTML (giá trị đã được set từ Blade template)
    const currentVehicleId = vehiclesSelect.value || vehiclesSelect.querySelector('option[selected]')?.value;
    
    console.log('updateVehicleList - currentVehicleId:', currentVehicleId, 'showOnlyRental:', showOnlyRental);
    
    // Nếu không có xe nào được chọn, không cần cập nhật
    if (!currentVehicleId) {
        console.log('Không có xe nào được chọn, bỏ qua cập nhật danh sách');
        return;
    }
    
    // Lấy danh sách xe từ window.vehicles (sẽ được set từ Blade template)
    if (window.vehicles && Array.isArray(window.vehicles) && window.vehicles.length > 0) {
        let hasCurrentVehicle = false;
        let availableVehicles = [];
        
        // Lọc xe theo điều kiện
        window.vehicles.forEach(vehicle => {
            if (!showOnlyRental || vehicle.is_car_rental) {
                availableVehicles.push(vehicle);
            }
        });
        
        // Kiểm tra xem xe hiện tại có trong danh sách filter không
        const currentVehicle = window.vehicles.find(v => v.vehicle_id == currentVehicleId);
        const isCurrentVehicleAvailable = currentVehicle && (!showOnlyRental || currentVehicle.is_car_rental);
        
        // Nếu xe hiện tại không có sẵn trong filter, không cập nhật để tránh mất selection
        if (!isCurrentVehicleAvailable) {
            console.log('Xe hiện tại không có sẵn trong filter, bỏ qua cập nhật để giữ nguyên selection');
            return;
        }
        
        // Xóa tất cả options hiện tại (trừ option đầu tiên)
        while (vehiclesSelect.children.length > 1) {
            vehiclesSelect.removeChild(vehiclesSelect.lastChild);
        }
        
        // Tạo options cho xe có sẵn
        availableVehicles.forEach(vehicle => {
            const option = document.createElement('option');
            option.value = vehicle.vehicle_id;
            option.textContent = `${vehicle.plate_number}-${vehicle.vehicleType.name}${vehicle.is_car_rental ? ' (Thuê)' : ''}`;
            
            // Đánh dấu selected nếu là xe hiện tại
            if (vehicle.vehicle_id == currentVehicleId) {
                option.selected = true;
                hasCurrentVehicle = true;
                console.log('Đã selected xe:', vehicle.plate_number, 'vehicle_id:', vehicle.vehicle_id);
            }
            
            vehiclesSelect.appendChild(option);
        });
        
        console.log(`Đã cập nhật danh sách xe: ${showOnlyRental ? 'chỉ xe thuê' : 'tất cả xe'}, tổng: ${vehiclesSelect.children.length - 1} xe, xe được chọn: ${currentVehicleId}`);
        
        // Kiểm tra xem có xe nào được selected không
        const selectedOption = vehiclesSelect.querySelector('option[selected]');
        if (selectedOption) {
            console.log('Xe được selected:', selectedOption.value, selectedOption.textContent);
        } else {
            console.log('Không có xe nào được selected!');
        }
    } else {
        console.log('window.vehicles chưa được khởi tạo hoặc không phải array:', window.vehicles);
    }
}

// Hàm xử lý checkbox "Xe HPL Thuê"
function toggleDriverSections() {
    const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
    const driverSection = document.getElementById('drivers');
    const hiddenInput = document.getElementById('is_car_rental_value');
    
    if (!isCarRentalCheckbox || !driverSection) {
        return; // Exit if elements don't exist
    }
    
    const isChecked = isCarRentalCheckbox.checked;
    
    // Cập nhật hidden input
    if (hiddenInput) {
        hiddenInput.value = isChecked ? '1' : '0';
        console.log('Updated hidden input is_car_rental_value:', hiddenInput.value);
    }
    
    if (isChecked) {
        // Nếu là xe thuê, hiện phần tài xế nhưng driver không bắt buộc
        driverSection.style.display = 'block';
        
        // Bỏ thuộc tính required cho tài xế
        const driverFields = driverSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
        driverFields.forEach(field => {
            field.removeAttribute('required');
            field.disabled = false; // Enable
        });
        
        console.log('Xe HPL thuê - hiện phần tài xế nhưng driver không bắt buộc');
    } else {
        // Nếu không phải xe thuê (xe công ty), hiện phần tài xế và yêu cầu driver
        driverSection.style.display = 'block';
        
        // Thêm thuộc tính required cho tài xế chính
        const driverFields = driverSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
        driverFields.forEach(field => {
            if (field.name.includes('[user_id]')) {
                field.setAttribute('required', 'required'); // Required cho user_id
            }
            field.disabled = false; // Enable
        });
        
        console.log('Xe công ty - hiện phần tài xế và yêu cầu driver');
    }
}

// Function để khởi tạo lại danh sách xe khi window.vehicles được set từ Blade template
function initializeVehicleList() {
    const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
    console.log('initializeVehicleList - isCarRentalCheckbox:', isCarRentalCheckbox, 'checked:', isCarRentalCheckbox?.checked);
    console.log('initializeVehicleList - window.vehicles:', window.vehicles);
    
    if (isCarRentalCheckbox && window.vehicles && Array.isArray(window.vehicles)) {
        // Chỉ cập nhật danh sách xe nếu checkbox được checked và thực sự cần filter
        if (isCarRentalCheckbox.checked) {
            console.log('Gọi updateVehicleList với is_car_rental: true');
            updateVehicleList(true);
        } else {
            console.log('is_car_rental = false, không cần filter xe');
        }
        console.log('Đã khởi tạo danh sách xe với', window.vehicles.length, 'xe');
    } else {
        console.log('Không thể khởi tạo danh sách xe:', {
            hasCheckbox: !!isCarRentalCheckbox,
            hasVehicles: !!window.vehicles,
            isArray: Array.isArray(window.vehicles),
            vehiclesLength: window.vehicles?.length
        });
    }
}

// Khởi tạo các sự kiện khi trang đã tải xong
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo form với số lượng driver ban đầu
    initCarRentalForm();
    
    // Cập nhật trạng thái nút thêm tài xế
    updateAddPersonButtonState();
    
    // Khai báo các biến cần thiết trước khi sử dụng
    const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
    const driverSection = document.getElementById('drivers');
    const vehiclesSelect = document.getElementById('vehicles');
    
    // Khởi tạo danh sách xe dựa trên trạng thái checkbox
    if (isCarRentalCheckbox) {
        // Gọi lần đầu để set trạng thái ban đầu cho driver sections
        toggleDriverSections();
        
        // Chỉ cập nhật danh sách xe nếu checkbox được checked và có dữ liệu xe
        if (isCarRentalCheckbox.checked && window.vehicles && Array.isArray(window.vehicles) && window.vehicles.length > 0) {
            console.log('Khởi tạo danh sách xe với', window.vehicles.length, 'xe, is_car_rental: true');
            // Không gọi updateVehicleList ngay lập tức để tránh mất selection
            console.log('Bỏ qua cập nhật danh sách xe để giữ nguyên selection từ Blade template');
        } else if (!isCarRentalCheckbox.checked) {
            console.log('is_car_rental = false, không cần filter xe');
        } else {
            console.log('Chưa có dữ liệu xe, chờ khởi tạo...');
        }
    }
    
    // Khởi tạo lại danh sách xe sau khi các function khác đã được khởi tạo
    setTimeout(() => {
        console.log('Timeout - Khởi tạo lại danh sách xe...');
        initializeVehicleList();
    }, 500); // Tăng timeout để đảm bảo Blade template load xong hoàn toàn
    
    // Thêm event listener cho nút thêm người
    const addPersonBtn = document.getElementById('addPersonBtn');
    if (addPersonBtn) {
        addPersonBtn.onclick = function() {
            addDriverRow(personTable, personDeductionTypes, window.users);
        };
    }
    
    // Thêm event listener cho nút thêm lơ xe
    const addPersonPxBtn = document.getElementById('addPersonPxBtn');
    if (addPersonPxBtn) {
        addPersonPxBtn.onclick = function() {
            addDriverPXRow(personPxTable, personPxDeductionTypes, window.userPXs);
        };
    }
    
    // Kiểm tra và cập nhật trạng thái nút thêm nhân sự dựa trên số lượng người dùng khả dụng
    updateAddPersonButtonState();
    
    // Định dạng tất cả các trường số khi trang được tải
    formatAllNumericInputs();
    
    // Format odometer và parking fee inputs khi load trang
    formatOdometerAndParkingFeeInputs();
    
    // Kiểm tra và chuyển đến tab có lỗi nếu có
    handleFormErrors();
    
    // Xử lý checkbox "Xe HPL Thuê"
    // const driverSection = document.getElementById('drivers'); // Moved up
    // const vehiclesSelect = document.getElementById('vehicles'); // Moved up
    
    // Thêm event listener cho checkbox
    if (isCarRentalCheckbox) {
        isCarRentalCheckbox.addEventListener('change', toggleDriverSections);
        
        // Gọi lần đầu để set trạng thái ban đầu
        toggleDriverSections();
    }
    
    // Xử lý submit form
    const form = document.getElementById('shipmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateCarRentalForm()) {
                prepareCarRentalFormBeforeSubmit();
                this.submit();
            }
        });
    }

    // Tính toán OT mới theo yêu cầu issue #180
    function calculateOvertime() {
        const startTime = document.getElementById('start_time')?.value;
        const endTime = document.getElementById('end_time')?.value;
        const runDate = document.querySelector('input[name="run_date"]')?.value;
        const overtimeRate = parseFloat((document.getElementById('overtime_rate')?.value || '50000').replace(/,/g, ''));
        const isOvertimeAtNoon = document.querySelector('input[name="is_overtime_at_noon"]')?.checked || false;
        
        if (!startTime || !endTime || !runDate) {
            return;
        }
        
        // Tạo đối tượng thời gian
        const startDateTime = new Date(runDate + ' ' + startTime);
        const endDateTime = new Date(runDate + ' ' + endTime);
        const overtimeStart = new Date(runDate + ' 17:30');
        
        let workingHours = 0; // Giờ làm việc thực tế (từ 17:30)
        let noonOvertimeHours = 0; // Giờ tăng ca trưa
        
        // Tính OT dựa trên giờ kết thúc thực tế (không phải 17:30 cố định)
        if (endDateTime > overtimeStart) {
            const effectiveStart = startDateTime > overtimeStart ? startDateTime : overtimeStart;
            workingHours = (endDateTime - effectiveStart) / (1000 * 60 * 60);
        }
        
        // Thêm tăng ca trưa 1h nếu có chọn checkbox
        if (isOvertimeAtNoon) {
            noonOvertimeHours = 1;
        }
        
        // Tổng giờ OT = Giờ làm việc + Tăng ca trưa
        const totalOvertimeHours = workingHours + noonOvertimeHours;
        const totalOvertimeCost = overtimeRate * totalOvertimeHours;
        
        // Hiển thị kết quả tính toán
        const overtimeHoursDisplay = document.getElementById('overtime_hours_display');
        const totalOvertimeCostDisplay = document.getElementById('total_overtime_cost_display');
        const workingHoursDisplay = document.getElementById('working_hours_display');
        const noonOvertimeDisplay = document.getElementById('noon_overtime_display');
        
        if (overtimeHoursDisplay) {
            overtimeHoursDisplay.textContent = totalOvertimeHours.toFixed(2) + ' giờ';
        }
        if (totalOvertimeCostDisplay) {
            totalOvertimeCostDisplay.textContent = totalOvertimeCost.toLocaleString('vi-VN') + ' VNĐ';
        }
        if (workingHoursDisplay) {
            workingHoursDisplay.textContent = workingHours.toFixed(2) + ' giờ';
        }
        if (noonOvertimeDisplay) {
            noonOvertimeDisplay.textContent = noonOvertimeHours.toFixed(2) + ' giờ';
        }
        
        // Cập nhật hidden fields
        const calculatedOvertimeHours = document.querySelector('input[name="calculated_overtime_hours"]');
        const calculatedTotalOvertimeCost = document.querySelector('input[name="calculated_total_overtime_cost"]');
        const workingHoursInput = document.querySelector('input[name="working_hours"]');
        const noonOvertimeHoursInput = document.querySelector('input[name="noon_overtime_hours"]');
        
        if (calculatedOvertimeHours) {
            calculatedOvertimeHours.value = totalOvertimeHours.toFixed(2);
        }
        if (calculatedTotalOvertimeCost) {
            calculatedTotalOvertimeCost.value = totalOvertimeCost;
        }
        if (workingHoursInput) {
            workingHoursInput.value = workingHours.toFixed(2);
        }
        if (noonOvertimeHoursInput) {
            noonOvertimeHoursInput.value = noonOvertimeHours.toFixed(2);
        }
        
        console.log('OT Calculation:', {
            startTime,
            endTime,
            runDate,
            overtimeRate,
            isOvertimeAtNoon,
            workingHours: workingHours.toFixed(2),
            noonOvertimeHours: noonOvertimeHours.toFixed(2),
            totalOvertimeHours: totalOvertimeHours.toFixed(2),
            totalOvertimeCost: totalOvertimeCost.toLocaleString('vi-VN')
        });
    }

    // Event listeners cho tính toán OT
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const runDateInput = document.querySelector('input[name="run_date"]');
    const overtimeRateInput = document.getElementById('overtime_rate');
    const isOvertimeAtNoonInput = document.querySelector('input[name="is_overtime_at_noon"]');

    if (startTimeInput) startTimeInput.addEventListener('change', calculateOvertime);
    if (endTimeInput) endTimeInput.addEventListener('change', calculateOvertime);
    if (runDateInput) runDateInput.addEventListener('change', calculateOvertime);
    if (overtimeRateInput) overtimeRateInput.addEventListener('change', calculateOvertime);
    
    // Thêm event listener đặc biệt cho checkbox is_overtime_at_noon
    if (isOvertimeAtNoonInput) {
        isOvertimeAtNoonInput.addEventListener('change', function() {
            // Gọi lại function calculateOvertime để tính toán lại toàn bộ
            calculateOvertime();
            
            console.log('Checkbox is_overtime_at_noon changed:', this.checked ? 'checked' : 'unchecked');
        });
    }
    
    // Format number input cho overtime_rate
    // if (overtimeRateInput) {
    //     overtimeRateInput.addEventListener('input', function() {
    //         let value = this.value;
    //         value = value.replace(/[^0-9]/g, '');
    //         this.value = parseInt(value).toLocaleString('vi-VN');
    //     });
    // }
    
    // Chạy tính toán lần đầu
    calculateOvertime();
});

// Validation cho car-rental form
function validateCarRentalForm() {
    let isValid = true;
    let errorMessage = '';
    let errorField = null;
    let firstInvalidField = null;
    
    console.log('=== BẮT ĐẦU VALIDATION ===');
    
    // 1. Kiểm tra các trường bắt buộc ở tab "Thông tin chuyến"
    const requiredFields = [
        { name: 'run_date', label: 'Ngày chạy' },
        { name: 'start_time', label: 'Giờ bắt đầu' },
        { name: 'end_time', label: 'Giờ kết thúc' },
        { name: 'start_location', label: 'Vị trí đi' },
        { name: 'end_location', label: 'Vị trí đến' },
        { name: 'start_odometer', label: 'Km bắt đầu' },
        { name: 'end_odometer', label: 'Km kết thúc' },
        { name: 'overtime_rate', label: 'Đơn giá tăng ca' },
        { name: 'status', label: 'Trạng thái' }
    ];
    
    // Kiểm tra từng trường bắt buộc
    for (const field of requiredFields) {
        const element = document.querySelector(`[name="${field.name}"]`);
        if (!element || !element.value.trim()) {
            element?.classList.add('is-invalid');
            if (!errorMessage) {
                errorMessage = `Vui lòng nhập ${field.label}!`;
                errorField = `[name="${field.name}"]`;
                firstInvalidField = element;
                console.log(`Lỗi đầu tiên: ${field.label} - ${field.name}`);
            }
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    }
    
    // 2. Kiểm tra hidden fields bắt buộc
    const customerIdField = document.querySelector('input[name="customer_id"]');
    const carRentalIdField = document.querySelector('input[name="car_rental_id"]');
    
    if (!customerIdField || !customerIdField.value) {
        customerIdField?.classList.add('is-invalid');
        if (!errorMessage) {
            errorMessage = 'Không tìm thấy thông tin khách hàng!';
            errorField = 'input[name="customer_id"]';
            firstInvalidField = customerIdField;
            console.log('Lỗi đầu tiên: Không tìm thấy thông tin khách hàng');
        }
        isValid = false;
    } else {
        customerIdField.classList.remove('is-invalid');
    }
    
    if (!carRentalIdField || !carRentalIdField.value) {
        carRentalIdField?.classList.add('is-invalid');
        if (!errorMessage) {
            errorMessage = 'Không tìm thấy thông tin thuê xe!';
            errorField = 'input[name="car_rental_id"]';
            firstInvalidField = carRentalIdField;
            console.log('Lỗi đầu tiên: Không tìm thấy thông tin thuê xe');
        }
        isValid = false;
    } else {
        carRentalIdField.classList.remove('is-invalid');
    }
    
    // 3. Kiểm tra vehicle_id
    const vehicleField = document.querySelector('[name="vehicle_id"]');
    if (!vehicleField || !vehicleField.value) {
        vehicleField?.classList.add('is-invalid');
        if (!errorMessage) {
            errorMessage = 'Vui lòng chọn phương tiện!';
            errorField = '[name="vehicle_id"]';
            firstInvalidField = vehicleField;
            console.log('Lỗi đầu tiên: Vui lòng chọn phương tiện');
        }
        isValid = false;
    } else {
        vehicleField.classList.remove('is-invalid');
    }
    
    // 4. Kiểm tra logic thời gian
    const startTime = document.querySelector('[name="start_time"]')?.value;
    const endTime = document.querySelector('[name="end_time"]')?.value;
    
    if (startTime && endTime) {
        const startDateTime = new Date(`2000-01-01T${startTime}`);
        const endDateTime = new Date(`2000-01-01T${endTime}`);
        
        if (endDateTime <= startDateTime) {
            const endTimeField = document.querySelector('[name="end_time"]');
            endTimeField?.classList.add('is-invalid');
            if (!errorMessage) {
                errorMessage = 'Giờ kết thúc phải sau giờ bắt đầu!';
                errorField = '[name="end_time"]';
                firstInvalidField = endTimeField;
                console.log('Lỗi đầu tiên: Giờ kết thúc phải sau giờ bắt đầu');
            }
            isValid = false;
        } else {
            document.querySelector('[name="end_time"]')?.classList.remove('is-invalid');
        }
    }
    
    // 5. Kiểm tra logic odometer
    const startOdometer = parseFloat(document.querySelector('[name="start_odometer"]')?.value.replace(/,/g, '') || '0');
    const endOdometer = parseFloat(document.querySelector('[name="end_odometer"]')?.value.replace(/,/g, '') || '0');
    
    if (startOdometer >= 0 && endOdometer >= 0 && endOdometer <= startOdometer) {
        const endOdometerField = document.querySelector('[name="end_odometer"]');
        endOdometerField?.classList.add('is-invalid');
        if (!errorMessage) {
            errorMessage = 'Km kết thúc phải lớn hơn km bắt đầu!';
            errorField = '[name="end_odometer"]';
            firstInvalidField = endOdometerField;
            console.log('Lỗi đầu tiên: Km kết thúc phải lớn hơn km bắt đầu');
        }
        isValid = false;
    } else {
        document.querySelector('[name="end_odometer"]')?.classList.remove('is-invalid');
    }
    
    // 6. Kiểm tra checkbox "Xe HPL Thuê"
    const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
    const driverSection = document.getElementById('drivers');
    
    console.log('Checkbox is_car_rental:', isCarRentalCheckbox?.checked);
    
    if (isCarRentalCheckbox && isCarRentalCheckbox.checked) {
        // Nếu là xe HPL thuê (is_car_rental = true), chỉ cần kiểm tra xe
        console.log('Xe HPL thuê - chỉ validate chọn xe, bỏ qua validation tài xế');
        
        // Không cần kiểm tra thông tin tài xế
        // Chỉ cần kiểm tra vehicle_id (đã kiểm tra ở bước 3)
        
    } else {
        // Nếu không phải xe HPL thuê (is_car_rental = false), kiểm tra cả xe và tài xế
        console.log('Không phải xe HPL thuê - validate cả xe và tài xế');
        
        // Kiểm tra thông tin tài xế
        const driverRows = document.querySelectorAll('#personTable tbody tr');
        let hasValidDriver = false;
        
        driverRows.forEach(row => {
            const driverSelect = row.querySelector('select[name*="[user_id]"]');
            if (driverSelect && driverSelect.value) {
                hasValidDriver = true;
            }
        });
        
        if (!hasValidDriver) {
            if (!errorMessage) {
                errorMessage = 'Vui lòng chọn ít nhất một tài xế!';
                errorField = '#personTable select[name*="[user_id]"]';
                firstInvalidField = document.querySelector('#personTable select[name*="[user_id]"]');
                console.log('Lỗi đầu tiên: Vui lòng chọn ít nhất một tài xế');
            }
            isValid = false;
        }
    }
    
    // 7. Kiểm tra toll fees nếu có (cho phép null)
    const tollFeeRows = document.querySelectorAll('#tollFeesTable tbody tr');
    if (tollFeeRows.length > 0) {
        for (const row of tollFeeRows) {
            const stationName = row.querySelector('input[name*="[station_name]"]')?.value?.trim();
            const transactionCode = row.querySelector('input[name*="[transaction_code]"]')?.value?.trim();
            const feeAmount = row.querySelector('input[name*="[fee_amount]"]')?.value?.trim();
            
            // Chỉ validate nếu có ít nhất một trường được điền
            const hasAnyValue = stationName || transactionCode || feeAmount;
            
            if (hasAnyValue) {
                // Nếu có điền thông tin thì phải điền đầy đủ
                if (!stationName || !transactionCode || !feeAmount) {
                    if (!errorMessage) {
                        errorMessage = 'Nếu nhập phí cầu đường thì vui lòng điền đầy đủ thông tin!';
                        errorField = '#tollFeesTable';
                        // Focus vào trường đầu tiên bị thiếu
                        if (!stationName) {
                            firstInvalidField = row.querySelector('input[name*="[station_name]"]');
                        } else if (!transactionCode) {
                            firstInvalidField = row.querySelector('input[name*="[transaction_code]"]');
                        } else if (!feeAmount) {
                            firstInvalidField = row.querySelector('input[name*="[fee_amount]"]');
                        }
                        console.log('Lỗi đầu tiên: Phí cầu đường thiếu thông tin');
                    }
                    isValid = false;
                    break;
                }
            }
            // Nếu không có trường nào được điền thì bỏ qua (cho phép null)
        }
    }
    
    console.log('=== KẾT QUẢ VALIDATION ===');
    console.log('isValid:', isValid);
    console.log('errorMessage:', errorMessage);
    console.log('errorField:', errorField);
    console.log('firstInvalidField:', firstInvalidField);
    
    // Hiển thị lỗi nếu có
    if (!isValid && errorMessage) {
        // Xóa tất cả thông báo lỗi cũ
        document.querySelectorAll('.text-danger').forEach(element => {
            element.textContent = '';
        });
        
        // Hiển thị thông báo lỗi với SweetAlert2
        Swal.fire({
            title: 'Lỗi validation!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Đóng',
            confirmButtonColor: '#d33'
        }).then(() => {
            // Chuyển đến tab chứa trường có lỗi
            let tabId = 'driverAllowance'; // Mặc định là tab thông tin chuyến
            
            if (errorField.includes('vehicle_id') || errorField.includes('#personTable')) {
                tabId = 'shipmentDetail'; // Tab phương tiện & tài xế
            }
            
            const tabLink = document.querySelector(`a[href="#${tabId}"]`);
            if (tabLink) {
                const tab = new bootstrap.Tab(tabLink);
                tab.show();
            }
            
            // Focus vào trường có lỗi đầu tiên
            if (firstInvalidField) {
                setTimeout(() => {
                    // Scroll đến element
                    firstInvalidField.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    // Focus vào element
                    firstInvalidField.focus();
                    
                    // Highlight element với animation
                    firstInvalidField.classList.add('highlight-error');
                    setTimeout(() => {
                        firstInvalidField.classList.remove('highlight-error');
                    }, 2000);
                }, 500); // Delay để tab chuyển xong
            }
        });
    }
    
    return isValid;
}

function prepareCarRentalFormBeforeSubmit() {
    console.log('Preparing car rental form before submit...');
    
    // Debug: Log tất cả form data trước khi submit
    const form = document.getElementById('shipmentForm');
    if (form) {
        const formData = new FormData(form);
        console.log('Form data before submit:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Đặc biệt kiểm tra status
        const statusSelect = document.querySelector('[name="status"]');
        if (statusSelect) {
            console.log('Status field:', {
                name: statusSelect.name,
                value: statusSelect.value,
                selectedOption: statusSelect.options[statusSelect.selectedIndex]?.text,
                allOptions: Array.from(statusSelect.options).map(opt => ({value: opt.value, text: opt.text, selected: opt.selected}))
            });
        }
    }
    
    // 1. Format các trường số trước khi submit
    const numericFields = ['start_odometer', 'end_odometer', 'overtime_rate', 'parking_fee'];
    numericFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && field.value) {
            // Xóa dấu phẩy và chuyển về số
            field.value = field.value.replace(/,/g, '');
            
            // Đảm bảo giá trị là số hợp lệ
            const numValue = parseFloat(field.value);
            if (!isNaN(numValue)) {
                field.value = numValue.toString();
            }
        }
    });
    
    // 2. Format toll fees
    const tollFeeAmounts = document.querySelectorAll('.toll-fee-amount');
    tollFeeAmounts.forEach(field => {
        if (field.value) {
            field.value = field.value.replace(/,/g, '');
        }
    });
    
    // 3. Đảm bảo format thời gian đúng
    const startTimeInput = document.querySelector('[name="start_time"]');
    const endTimeInput = document.querySelector('[name="end_time"]');
    
    if (startTimeInput && startTimeInput.value) {
        // Đảm bảo format H:i
        const startTime = startTimeInput.value;
        if (startTime.match(/^\d{1,2}:\d{2}$/)) {
            // Format đã đúng
        } else {
            // Cố gắng parse và format lại
            const time = new Date(`2000-01-01T${startTime}`);
            if (!isNaN(time.getTime())) {
                startTimeInput.value = time.toTimeString().slice(0, 5);
            }
        }
    }
    
    if (endTimeInput && endTimeInput.value) {
        // Đảm bảo format H:i
        const endTime = endTimeInput.value;
        if (endTime.match(/^\d{1,2}:\d{2}$/)) {
            // Format đã đúng
        } else {
            // Cố gắng parse và format lại
            const time = new Date(`2000-01-01T${endTime}`);
            if (!isNaN(time.getTime())) {
                endTimeInput.value = time.toTimeString().slice(0, 5);
            }
        }
    }
    
    // 4. Đảm bảo vehicle_id được gửi đúng
    const vehicleSelect = document.querySelector('[name="vehicle_id"]');
    if (vehicleSelect && vehicleSelect.value) {
        // Đảm bảo value là integer
        const vehicleId = vehicleSelect.value;
        if (vehicleId && !isNaN(parseInt(vehicleId))) {
            vehicleSelect.value = parseInt(vehicleId);
        }
        
        console.log('Vehicle ID prepared:', vehicleSelect.value);
    }
    
    // 5. Xử lý checkbox is_car_rental
    const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
    const hiddenInput = document.getElementById('is_car_rental_value');
    
    if (isCarRentalCheckbox) {
        // Luôn set value dựa trên trạng thái checked
        if (isCarRentalCheckbox.checked) {
            isCarRentalCheckbox.value = '1';
            if (hiddenInput) hiddenInput.value = '1';
            
            // Bỏ required nhưng KHÔNG disable và KHÔNG clear values để giữ nguyên dữ liệu
            const driversSection = document.querySelector('#drivers');
            if (driversSection) {
                const driverFields = driversSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
                driverFields.forEach(field => {
                    field.removeAttribute('required');
                    // KHÔNG disable và KHÔNG clear values
                    // field.disabled = true; // Bỏ dòng này
                    // if (field.tagName === 'SELECT') {
                    //     field.selectedIndex = 0; // Bỏ dòng này
                    // } else {
                    //     field.value = ''; // Bỏ dòng này
                    // }
                });
                
                console.log('Xe HPL thuê - đã bỏ required, giữ nguyên dữ liệu driver');
            }
        } else {
            isCarRentalCheckbox.value = '0';
            if (hiddenInput) hiddenInput.value = '0';
            
            // Thêm lại required và enable driver fields
            const driversSection = document.querySelector('#drivers');
            if (driversSection) {
                const driverFields = driversSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
                driverFields.forEach(field => {
                    if (field.name.includes('[user_id]')) {
                        field.setAttribute('required', 'required'); // Chỉ required cho user_id
                    }
                    field.disabled = false; // Enable lại
                });
                
                console.log('Không phải xe HPL thuê - đã thêm lại required và enable driver fields');
            }
        }
        
        console.log('is_car_rental prepared:', {
            'checked': isCarRentalCheckbox.checked,
            'checkbox_value': isCarRentalCheckbox.value,
            'hidden_value': hiddenInput ? hiddenInput.value : 'not_found'
        });
    }
    
    // 6. Debug: Log driver data
    const driversSection = document.querySelector('#drivers');
    if (driversSection) {
        const driverSelects = driversSection.querySelectorAll('select[name*="[user_id]"]');
        console.log('Driver selects found:', driverSelects.length);
        driverSelects.forEach((select, index) => {
            console.log(`Driver ${index}:`, {
                name: select.name,
                value: select.value,
                selectedOption: select.options[select.selectedIndex]?.text,
                disabled: select.disabled,
                required: select.hasAttribute('required')
            });
        });
    }
    
    // 7. Xử lý driver rows nếu không phải xe HPL thuê
    const isCarRental = document.querySelector('input[name="is_car_rental"]')?.checked;
    
    if (!isCarRental) {
        // Tạo một mảng để lưu trữ các chỉ số hàng driver đã được thêm vào
        const driverRows = [];
        document.querySelectorAll('input[name="driver_rows[]"]').forEach(input => {
            driverRows.push(input.value);
        });
        
        // Tạo một input hidden để lưu trữ các chỉ số hàng driver
        const driverRowsInput = document.createElement('input');
        driverRowsInput.type = 'hidden';
        driverRowsInput.name = 'driver_row_indexes';
        driverRowsInput.value = driverRows.join(',');
        document.getElementById('shipmentForm').appendChild(driverRowsInput);
        
        console.log('Driver rows prepared:', driverRowsInput.value);
    }
    
    // 8. Xử lý toll fee rows
    const tollFeeRows = [];
    document.querySelectorAll('#tollFeesTable tbody tr').forEach((row, index) => {
        if (row.querySelector('input[name*="[station_name]"]')?.value) {
            tollFeeRows.push(index);
        }
    });
    
    if (tollFeeRows.length > 0) {
        // Tạo một input hidden để lưu trữ các chỉ số hàng toll fee
        const tollFeeRowsInput = document.createElement('input');
        tollFeeRowsInput.type = 'hidden';
        tollFeeRowsInput.name = 'toll_fee_row_indexes';
        tollFeeRowsInput.value = tollFeeRows.join(',');
        document.getElementById('shipmentForm').appendChild(tollFeeRowsInput);
        
        console.log('Toll fee rows prepared:', tollFeeRowsInput.value);
    }
    
    // 9. Định dạng tất cả các trường số trước khi submit
    document.querySelectorAll('input[type="number"]').forEach(input => {
        if (input.value !== '') {
            const value = parseFloat(input.value);
            if (Number.isInteger(value)) {
                input.value = parseInt(value);
            }
        }
    });
    
    console.log('Car rental form prepared successfully');
}

// Toll Fee Management Functions
// Variables for toll fee row management
let tollFeeRowIndex = 0;

// Add toll fee row to create form
function addTollFeeRow() {
    // Sử dụng window.tollFeeRowIndex nếu có, nếu không thì dùng local variable
    const currentIndex = typeof window.tollFeeRowIndex !== 'undefined' ? window.tollFeeRowIndex : tollFeeRowIndex;
    
    const tbody = document.querySelector('#tollFeesTable tbody');
    const row = `
        <tr data-index="${currentIndex}">
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${currentIndex}][station_name]" placeholder="Tên trạm">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${currentIndex}][transaction_code]" placeholder="Mã giao dịch">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm toll-fee-amount" name="toll_fees[${currentIndex}][fee_amount]" placeholder="Số tiền">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${currentIndex}][notes]" placeholder="Ghi chú">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTollFeeRow(${currentIndex})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', row);
    
    // Increment the appropriate index
    if (typeof window.tollFeeRowIndex !== 'undefined') {
        window.tollFeeRowIndex++;
    } else {
        tollFeeRowIndex++;
    }
    
    // Initialize number formatting for new row
    initializeTollFeeFormatting();
}

// Remove toll fee row from create form
function removeTollFeeRow(index) {
    const row = document.querySelector(`#tollFeesTable tbody tr[data-index="${index}"]`);
    if (row) {
        row.remove();
    }
}

// Initialize number formatting for toll fee amounts in create form
function initializeTollFeeFormatting() {
    const tollFeeAmounts = document.querySelectorAll('.toll-fee-amount');
    tollFeeAmounts.forEach(field => {
        field.addEventListener('input', function() {
            let value = this.value;
            value = value.replace(/[^0-9.]/g, '');
            
            let parts = value.split('.');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
            
            this.value = integerPart + decimalPart;
        });
    });
}

// Helper functions
function initCarRentalForm() {
    // Khởi tạo form car-rental
    console.log('Car rental form initialized');
    
    // Khởi tạo các trường số
    formatAllNumericInputs();
    
    // Khởi tạo toll fee formatting
    initializeTollFeeFormatting();
}

function updateAddPersonButtonState() {
    // Cập nhật trạng thái nút thêm nhân sự
    console.log('Add person button state updated');
}

// Function để format tất cả các trường số
function formatAllNumericInputs() {
    // Format deduction inputs
    const deductionInputs = document.querySelectorAll('.deduction-input');
    deductionInputs.forEach(input => {
        if (input.value) {
            let value = input.value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.value = value;
            }
        }
    });
    
    // Format unit inputs
    const unitInputs = document.querySelectorAll('.unit-input');
    unitInputs.forEach(input => {
        if (input.value) {
            let value = input.value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.value = value;
            }
        }
    });
}

// Function để format odometer và parking fee inputs
function formatOdometerAndParkingFeeInputs() {
    // Format odometer inputs
    const odometerInputs = document.querySelectorAll('.odometer-input');
    odometerInputs.forEach(input => {
        if (input.value) {
            let value = input.value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.value = value;
            }
        }
    });
    
    // Format parking fee input
    const parkingFeeInputs = document.querySelectorAll('.parking-fee-input');
    parkingFeeInputs.forEach(input => {
        if (input.value) {
            let value = input.value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.value = value;
            }
        }
    });
}

function handleFormErrors() {
    // Xử lý lỗi form
    console.log('Form errors handled');
}

// Function để lấy danh sách user IDs đã được chọn
function getSelectedUserIds(table, type) {
    const selectedIds = [];
    const selects = table.querySelectorAll('select[name*="[user_id]"]');
    
    selects.forEach(select => {
        if (select.value) {
            selectedIds.push(parseInt(select.value));
        }
    });
    
    console.log('Selected ' + type + ' IDs:', selectedIds);
    return selectedIds;
}

// Function để thêm hàng tài xế mới
function addDriverRow(table, deductionTypes, users) {
    const currentRows = table.querySelectorAll('tr').length;
    const selectedIds = getSelectedUserIds(table, 'driver');
    
    // Tạo hàng mới
    const newRow = document.createElement('tr');
    const rowIndex = currentRows;
    
    // Tạo HTML cho hàng mới
    let html = `
        <td>
            <select name="drivers[${rowIndex}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                <option value="">Chọn nhân sự</option>`;
    
    // Thêm options cho users chưa được chọn
    for (const [id, name] of Object.entries(users)) {
        if (!selectedIds.includes(parseInt(id))) {
            html += `<option value="${id}">${name}</option>`;
        }
    }
    
    html += `
            </select>
        </td>
        <td class="text-center">
            <div class="form-check form-switch d-inline-block">
                <input type="checkbox" name="drivers[${rowIndex}][deductions][is_main_driver]" 
                    class="form-check-input deduction-input" value="1">
            </div>
        </td>`;
    
    // Thêm các cột cho deduction types
    deductionTypes.forEach(type => {
        html += `
        <td>
            <input type="text" name="drivers[${rowIndex}][deductions][${type.id}]" 
                class="form-control form-control-sm deduction-input" min="0">
        </td>`;
    });
    
    html += `
        <td>
            <input type="text" name="drivers[${rowIndex}][deductions][Ghi chú]" 
                class="form-control form-control-sm">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" 
                onclick="removeDriverRow(this, ${rowIndex})">
                <i class="ri-delete-bin-fill"></i>
            </button>
            <input type="hidden" name="driver_rows[]" value="${rowIndex}">
        </td>`;
    
    newRow.innerHTML = html;
    table.appendChild(newRow);
    
    // Format các input số mới
    const newInputs = newRow.querySelectorAll('.deduction-input');
    newInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (typeof window.formatPriceInput === 'function') {
                window.formatPriceInput(this);
            }
        });
    });
    
    console.log('Added driver row with index:', rowIndex);
}

// Function để thêm hàng lơ xe mới
function addDriverPXRow(table, deductionTypes, users) {
    const currentRows = table.querySelectorAll('tr').length;
    const selectedIds = getSelectedUserIds(table, 'driverPX');
    
    // Tạo hàng mới
    const newRow = document.createElement('tr');
    const rowIndex = currentRows;
    
    // Tạo HTML cho hàng mới
    let html = `
        <td>
            <select name="driverPXs[${rowIndex}][user_id]" class="form-select form-select-sm" 
                style="min-width: 180px;" required>
                <option value="">Chọn nhân sự</option>`;
    
    // Thêm options cho users chưa được chọn
    for (const [id, name] of Object.entries(users)) {
        if (!selectedIds.includes(parseInt(id))) {
            html += `<option value="${id}">${name}</option>`;
        }
    }
    
    html += `
            </select>
        </td>`;
    
    // Thêm các cột cho deduction types
    deductionTypes.forEach(type => {
        html += `
        <td>
            <input type="text" name="driverPXs[${rowIndex}][deductions][${type.id}]" 
                class="form-control form-control-sm deduction-input" min="0">
        </td>`;
    });
    
    html += `
        <td>
            <input type="text" name="driverPXs[${rowIndex}][deductions][Ghi chú]" 
                class="form-control form-control-sm">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" 
                onclick="removeDriverPXRow(this, ${rowIndex})">
                <i class="ri-delete-bin-fill"></i>
            </button>
            <input type="hidden" name="driverPX_rows[]" value="${rowIndex}">
        </td>`;
    
    newRow.innerHTML = html;
    table.appendChild(newRow);
    
    // Format các input số mới
    const newInputs = newRow.querySelectorAll('.deduction-input');
    newInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (typeof window.formatPriceInput === 'function') {
                window.formatPriceInput(this);
            }
        });
    });
    
    console.log('Added driver PX row with index:', rowIndex);
}

// Function để xóa hàng tài xế
function removeDriverRow(button, index) {
    const row = button.closest('tr');
    const table = row.closest('tbody');
    const currentRows = table.querySelectorAll('tr').length;
    
    // Không cho xóa nếu chỉ còn 1 hàng
    if (currentRows <= 1) {
        Swal.fire({
            title: 'Không thể xóa',
            text: 'Phải có ít nhất một tài xế',
            icon: 'warning',
            confirmButtonText: 'Đóng'
        });
        return;
    }
    
    row.remove();
    console.log('Removed driver row:', index);
}

// Function để xóa hàng lơ xe
function removeDriverPXRow(button, index) {
    const row = button.closest('tr');
    row.remove();
    console.log('Removed driver PX row:', index);
} 