/**
 * Shipment form handling
 * Contains common functions for shipment create and edit forms
 */

// Biến để theo dõi số lượng hàng driver đã thêm vào
let driverRowCount = 0;

// Biến toàn cục để lưu trữ danh sách người dùng
let users = {};

/**
 * Hàm để lấy danh sách user_id đã được chọn
 * @param {HTMLElement} tableElement - Phần tử bảng chứa các dropdown
 * @param {string} userType - Loại người dùng ('driver' hoặc 'assistant')
 * @returns {Array} Mảng các user_id đã được chọn
 */
function getSelectedUserIds(tableElement, userType = 'driver') {
    const selectedIds = [];
    
    // Kiểm tra nếu tableElement không tồn tại
    if (!tableElement) {
        console.warn('Table element not found for user type:', userType);
        return selectedIds;
    }
    
    const selector = userType === 'driver' 
        ? 'select[name^="drivers"][name$="[user_id]"]' 
        : 'select[name^="driverPXs"][name$="[user_id]"]';
    
    try {
        const selects = tableElement.querySelectorAll(selector);
        selects.forEach(select => {
            if (select && select.value) {
                selectedIds.push(select.value);
            }
        });
    } catch (error) {
        console.error('Error getting selected user IDs:', error);
    }
    
    return selectedIds;
}

/**
 * Hàm xử lý khi xóa một hàng driver
 * @param {HTMLElement} button - Nút xóa được nhấn
 * @param {number} rowIndex - Chỉ số hàng
 */
function removeDriverRow(button, rowIndex) {
    // Xóa hàng từ bảng
    button.closest('tr').remove();
    
    // Cập nhật lại danh sách dropdown và trạng thái nút thêm
    updateUserDropdowns();
    
    // Cập nhật trạng thái nút thêm nhân sự
    updateAddPersonButtonState();
}

/**
 * Hàm xử lý khi xóa một hàng hàng hóa
 * @param {HTMLElement} button - Nút xóa được nhấn
 * @param {number} rowIndex - Chỉ số hàng
 */
function removeGoodRow(button, rowIndex) {
    // Xóa hàng từ bảng
    button.closest('tr').remove();
}

/**
 * Hàm để cập nhật các dropdown user_id
 * Ẩn/vô hiệu hóa các option đã được chọn ở các dropdown khác
 */
function updateUserDropdowns() {
    const selectedIds = getSelectedUserIds(personTable, 'driver');
    
    // Cập nhật tất cả các dropdown
    document.querySelectorAll('select[name$="[user_id]"]').forEach(select => {
        const currentValue = select.value;
        
        // Lưu lại giá trị hiện tại
        Array.from(select.options).forEach(option => {
            // Bỏ qua option rỗng (placeholder)
            if (!option.value) return;
            
            // Nếu option này đã được chọn ở dropdown khác, ẩn nó đi
            if (selectedIds.includes(option.value) && option.value !== currentValue) {
                option.disabled = true;
                option.style.display = 'none';
            } else {
                option.disabled = false;
                option.style.display = '';
            }
        });
    });
}

/**
 * Thêm một hàng driver mới vào bảng
 * @param {HTMLElement} personTable - Bảng chứa các hàng driver
 * @param {Array} personDeductionTypes - Mảng các loại phụ cấp
 * @param {Object} users - Object chứa danh sách người dùng (id => name)
 * @returns {boolean} - Trả về true nếu thêm thành công, false nếu không thể thêm
 */
function addDriverRow(personTable, personDeductionTypes, users) {
    // Kiểm tra xem còn người dùng khả dụng không
    if (!users || Object.keys(users).length === 0) {
        console.error('Không có tài xế nào khả dụng');
        Swal.fire({
            title: 'Lỗi',
            text: 'Không có tài xế nào khả dụng. Vui lòng thêm tài xế trước.',
            icon: 'error',
            confirmButtonText: 'Đóng'
        });
        return false;
    }
    
    const selectedIds = getSelectedUserIds(personTable, 'driver');
    const totalUsers = Object.keys(users).length;
    
    console.log('Selected IDs:', selectedIds.length, 'Total Users:', totalUsers);
    
    // Nếu đã chọn hết tất cả người dùng, không cho thêm nữa
    if (selectedIds.length >= totalUsers) {
        Swal.fire({
            title: 'Không thể thêm',
            text: 'Đã sử dụng hết tất cả nhân sự có sẵn',
            icon: 'warning',
            confirmButtonText: 'Đóng'
        });
        return false;
    }
    
    // Tăng số lượng hàng
    driverRowCount++;
    
    let deductionInputs = '';
    personDeductionTypes.forEach(type => {
        deductionInputs += `<td>
            <input type="hidden" name="drivers[${driverRowCount}][deduction_type_ids][]" value="${type.id}">
            <input type="text" name="drivers[${driverRowCount}][deductions][${type.id}]" class="form-control form-control-sm deduction-input" min="0">
        </td>`;
    });
    
    const row = document.createElement('tr');
    
    // Tạo HTML cho dropdown với các option đã lọc
    let userOptionsHtml = '<option value="">Chọn nhân sự</option>';
    
    for (const id in users) {
        if (users.hasOwnProperty(id)) {
            userOptionsHtml += `<option value="${id}" ${selectedIds.includes(id) ? 'disabled style="display:none;"' : ''}>${users[id]}</option>`;
        }
    }
    
    row.innerHTML = `
        <td>
            <select name="drivers[${driverRowCount}][user_id]" class="form-select form-select-sm" required>
                ${userOptionsHtml}
            </select>
            <div class="text-danger" id="error-drivers-${driverRowCount}-user_id"></div>
        </td>
        <td class="text-center">
            <div class="form-check form-switch d-inline-block">
                <input type="checkbox" name="drivers[${driverRowCount}][deductions][is_main_driver]" class="form-check-input deduction-input" value="1" 
                    ${(window.laravelOld && window.laravelOld[`drivers.${driverRowCount}.deductions.is_main_driver`]) || (typeof $driver !== 'undefined' && $driver && $driver.deductions && $driver.deductions.is_main_driver) ? 'checked' : ''}>
            </div>
            <div class="text-danger" id="error-drivers-${driverRowCount}-deductions-is_main_driver"></div>
        </td>
        ${deductionInputs}
        <td>
            <input type="text" name="drivers[${driverRowCount}][deductions][notes]" class="form-control form-control-sm">
            <div class="text-danger" id="error-drivers-${driverRowCount}-deductions-notes"></div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, ${driverRowCount})"><i class="ri-delete-bin-fill"></i></button>
            <input type="hidden" name="driver_rows[]" value="${driverRowCount}">
        </td>
    `;
    personTable.appendChild(row);
    
    // Thêm event listener cho dropdown mới
    const newSelect = row.querySelector(`select[name="drivers[${driverRowCount}][user_id]"]`);
    newSelect.addEventListener('change', updateUserDropdowns);
    
    // Thêm event listener cho các trường số mới thêm vào
    addNumericInputListeners(row.querySelectorAll('input[type="number"]'));
    
    // Apply VND formatting cho các input deduction mới thêm vào
    const newDeductionInputs = row.querySelectorAll('.deduction-input');
    newDeductionInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value;
            
            // Remove non-numeric characters and handle decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // If there's a decimal part, handle it
            if (value.includes('.')) {
                // Split into integer and decimal parts
                let parts = value.split('.');
                // If decimal part is .00, remove it completely
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0];
                } else {
                    // Otherwise keep only integer part
                    value = parts[0];
                }
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            this.value = value;
        });
    });
    
    // Kiểm tra nếu đã sử dụng hết tất cả người dùng, vô hiệu hóa nút thêm
    updateAddPersonButtonState();
    
    return true;
}

/**
 * Thêm một hàng hàng hóa mới vào bảng
 * @param {HTMLElement} goodsTable - Bảng chứa các hàng hàng hóa
 * @param {number} goodsCount - Chỉ số hàng hàng hóa hiện tại
 * @returns {number} Chỉ số hàng hàng hóa mới
 */
function addGoodRow(goodsTable, goodsCount) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="text" name="goods[${goodsCount}][name]" class="form-control form-control-sm" required>
            <div class="text-danger" id="error-goods-${goodsCount}-name"></div>
        </td>
        <td>
            <input type="text" name="goods[${goodsCount}][notes]" class="form-control form-control-sm">
            <div class="text-danger" id="error-goods-${goodsCount}-notes"></div>
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][quantity]" class="form-control form-control-sm" min="1">
            <div class="text-danger" id="error-goods-${goodsCount}-quantity"></div>
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][weight]" class="form-control form-control-sm" min="0">
            <div class="text-danger" id="error-goods-${goodsCount}-weight"></div>
        </td>
        <td>
            <input type="text" name="goods[${goodsCount}][unit]" class="form-control form-control-sm unit-input">
            <div class="text-danger" id="error-goods-${goodsCount}-unit"></div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGoodRow(this, ${goodsCount})"><i class="ri-delete-bin-fill"></i></button>
            <input type="hidden" name="goods_rows[]" value="${goodsCount}">
        </td>
    `;
    goodsTable.appendChild(row);
    
    // Thêm event listener cho các trường số mới thêm vào (không bao gồm unit-input)
    const numericInputs = row.querySelectorAll('input[type="number"]:not(.unit-input)');
    addNumericInputListeners(numericInputs);
    
    // Apply VND formatting cho các unit input mới thêm vào
    const unitInputs = row.querySelectorAll('.unit-input');
    unitInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Use the global formatPriceInput function if available
            if (typeof window.formatPriceInput === 'function') {
                window.formatPriceInput(this);
            } else {
                // Fallback if the global function is not available
                let value = this.value;
                
                // Remove non-numeric characters and handle decimal part
                value = value.replace(/[^0-9.]/g, '');
                
                // If there's a decimal part, handle it
                if (value.includes('.')) {
                    // Split into integer and decimal parts
                    let parts = value.split('.');
                    // If decimal part is .00, remove it completely
                    if (parts[1] === '00' || parts[1] === '0') {
                        value = parts[0];
                    } else {
                        // Otherwise keep only integer part
                        value = parts[0];
                    }
                }
                
                // Allow up to 9 digits for the unit input
                if (value.length > 9) {
                    value = value.substring(0, 9);
                }
                
                // Format with commas
                if (value) {
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
                
                this.value = value;
            }
        });
    });
    
    return goodsCount + 1;
}

/**
 * Thêm event listener cho các trường số
 * @param {NodeList} inputs - Danh sách các input số
 */
function addNumericInputListeners(inputs) {
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    });
}

/**
 * Kiểm tra và cập nhật trạng thái nút thêm nhân sự dựa trên số lượng người dùng khả dụng
 */
function updateAddPersonButtonState() {
    try {
        const addPersonBtn = document.getElementById('addPersonBtn');
        if (!addPersonBtn) {
            console.error('Add person button not found');
            return;
        }

        // Kiểm tra nếu window.users chưa được khởi tạo hoặc rỗng
        if (!window.users || Object.keys(window.users).length === 0) {
            console.warn('No users available');
            addPersonBtn.disabled = true;
            addPersonBtn.classList.remove('btn-outline-primary');
            addPersonBtn.classList.add('btn-outline-secondary');
            
            // Hiển thị thông báo cho người dùng
            const message = 'Không có tài xế nào khả dụng. Vui lòng thêm tài xế trước.';
            const existingAlert = document.getElementById('no-drivers-alert');
            
            // if (!existingAlert) {
            //     const alertDiv = document.createElement('div');
            //     alertDiv.id = 'no-drivers-alert';
            //     alertDiv.className = 'alert alert-warning mt-3';
            //     alertDiv.textContent = message;
                
            //     // Chèn thông báo vào trước bảng
            //     const table = document.querySelector('#personTable');
            //     if (table) {
            //         table.parentNode.insertBefore(alertDiv, table);
            //     }
            // }
            
            return;
        }

        // Ẩn thông báo nếu có
        const existingAlert = document.getElementById('no-drivers-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const selectedIds = getSelectedUserIds();
        const totalUsers = Object.keys(window.users).length;
        
        console.log('updateAddPersonButtonState - Selected IDs:', selectedIds, 'Total Users:', totalUsers);
        
        if (selectedIds.length >= totalUsers) {
            addPersonBtn.disabled = true;
            addPersonBtn.classList.remove('btn-outline-primary');
            addPersonBtn.classList.add('btn-outline-secondary');
            
            // Hiển thị thông báo nếu đã chọn tất cả tài xế
            const existingAllSelectedAlert = document.getElementById('all-drivers-selected-alert');
            if (!existingAllSelectedAlert) {
                const alertDiv = document.createElement('div');
                alertDiv.id = 'all-drivers-selected-alert';
                alertDiv.className = 'alert alert-info mt-3';
                alertDiv.textContent = 'Đã chọn tất cả tài xế có sẵn.';
                
                const table = document.querySelector('#personTable');
                if (table) {
                    table.parentNode.insertBefore(alertDiv, table.nextSibling);
                }
            }
        } else {
            addPersonBtn.disabled = false;
            addPersonBtn.classList.remove('btn-outline-secondary');
            addPersonBtn.classList.add('btn-outline-primary');
            
            // Ẩn thông báo nếu có
            const existingAllSelectedAlert = document.getElementById('all-drivers-selected-alert');
            if (existingAllSelectedAlert) {
                existingAllSelectedAlert.remove();
            }
        }
        
    } catch (error) {
        console.error('Error in updateAddPersonButtonState:', error);
        // Đảm bảo nút được bật nếu có lỗi
        const addPersonBtn = document.getElementById('addPersonBtn');
        if (addPersonBtn) {
            addPersonBtn.disabled = false;
            addPersonBtn.classList.remove('btn-outline-secondary');
            addPersonBtn.classList.add('btn-outline-primary');
        }
    }
}

/**
 * Chuẩn bị form trước khi submit
 * @param {HTMLFormElement} form - Form cần chuẩn bị
 */
function prepareFormBeforeSubmit(form) {
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
    form.appendChild(driverRowsInput);
    
    // Tạo một mảng để lưu trữ các chỉ số hàng hàng hóa đã được thêm vào
    const goodsRows = [];
    document.querySelectorAll('input[name="goods_rows[]"]').forEach(input => {
        goodsRows.push(input.value);
    });
    
    // Tạo một input hidden để lưu trữ các chỉ số hàng hàng hóa
    const goodsRowsInput = document.createElement('input');
    goodsRowsInput.type = 'hidden';
    goodsRowsInput.name = 'goods_row_indexes';
    goodsRowsInput.value = goodsRows.join(',');
    form.appendChild(goodsRowsInput);
    
    // Định dạng tất cả các trường số trước khi submit
    document.querySelectorAll('input[type="number"]').forEach(input => {
        if (input.value !== '') {
            const value = parseFloat(input.value);
            if (Number.isInteger(value)) {
                input.value = parseInt(value);
            }
        }
    });
}

/**
 * Xử lý lỗi form và hiển thị thông báo
 * @param {string} errorMessage - Thông báo lỗi
 * @param {string} errorField - Selector của trường có lỗi
 * @param {string} tabId - ID của tab chứa trường có lỗi
 */
function handleFormError(errorMessage, errorField, tabId) {
    // Hiển thị thông báo lỗi
    Swal.fire({
        title: 'Lỗi!',
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'Đóng',
        confirmButtonColor: '#d33'
    }).then(() => {
        // Chuyển đến tab chứa trường có lỗi
        const tabLink = document.querySelector(`a[href="#${tabId}"]`);
        if (tabLink) {
            const tab = new bootstrap.Tab(tabLink);
            tab.show();
            
            // Focus vào trường có lỗi
            setTimeout(() => {
                const errorElement = document.querySelector(errorField);
                if (errorElement) {
                    errorElement.focus();
                    errorElement.classList.add('highlight-error');
                    setTimeout(() => {
                        errorElement.classList.remove('highlight-error');
                    }, 2000);
                }
            }, 300);
        }
    });
}

/**
 * Kiểm tra form trước khi submit
 * @param {HTMLFormElement} form - Form cần kiểm tra
 * @returns {boolean} Kết quả kiểm tra
 */
function validateShipmentForm(form) {
    // 1. Kiểm tra các trường ở tab thông tin vận chuyển
    const customerId = form.querySelector('select[name="customer_id"]')?.value;
    const origin = form.querySelector('input[name="origin"]')?.value;
    // const destination = form.querySelector('input[name="destination"]')?.value;
    const departureTime = form.querySelector('input[name="departure_time"]')?.value;
    const estimatedArrivalTime = form.querySelector('input[name="estimated_arrival_time"]')?.value;
    
    // Kiểm tra xem có ít nhất một hàng hóa hay không
    const goodsNameInputs = form.querySelectorAll('input[name^="goods["][name$="][name]"]');
    const hasGoods = goodsNameInputs.length > 0 && Array.from(goodsNameInputs).some(input => input.value.trim() !== '');
    
    // Kiểm tra các trường ở tab thông tin vận chuyển
    if (!customerId || !origin || !departureTime || !estimatedArrivalTime || !hasGoods) {
        let errorMessage = '';
        let errorField = null;
        let tabId = 'driverAllowance'; // ID của tab thông tin vận chuyển
        
        if (!customerId) {
            errorMessage = 'Vui lòng chọn khách hàng!';
            errorField = 'select[name="customer_id"]';
        } else if (!origin) {
            errorMessage = 'Vui lòng nhập điểm khởi hành!';
            errorField = 'input[name="origin"]';
        } else if (!departureTime) {
            errorMessage = 'Vui lòng chọn thời gian khởi hành!';
            errorField = 'input[name="departure_time"]';
        } else if (!estimatedArrivalTime) {
            errorMessage = 'Vui lòng chọn thời gian dự kiến đến!';
            errorField = 'input[name="estimated_arrival_time"]';
        } else if (!hasGoods) {
            errorMessage = 'Vui lòng thêm ít nhất một hàng hóa!';
            errorField = 'input[name^="goods["][name$="][name]"]';
        }
        
        handleFormError(errorMessage, errorField, tabId);
        return false;
    }
    
    // 2. Kiểm tra chi tiết các hàng hóa
    let goodsValid = true;
    let goodsErrorMessage = '';
    let goodsErrorField = null;
    
    // Kiểm tra từng hàng hóa
    goodsNameInputs.forEach(input => {
        if (!goodsValid) return; // Nếu đã có lỗi, không kiểm tra tiếp
        
        const rowIndex = input.name.match(/\[(\d+)\]/)[1];
        const nameValue = input.value.trim();
        // const quantityInput = form.querySelector(`input[name="goods[${rowIndex}][quantity]"]`);
        // const quantityValue = quantityInput ? quantityInput.value.trim() : '';
        // const unitInput = form.querySelector(`input[name="goods[${rowIndex}][unit]"]`);
        // const unitValue = unitInput ? unitInput.value.trim() : '';
        
        // Kiểm tra tên hàng hóa
        if (nameValue === '') {
            goodsErrorMessage = 'Tên hàng hóa không được để trống!';
            goodsErrorField = input;
            goodsValid = false;
            // Hiển thị lỗi trực tiếp trong trường
            document.getElementById(`error-goods-${rowIndex}-name`).textContent = goodsErrorMessage;
            return;
        }
        
        // Kiểm tra số lượng
        // if (quantityValue === '' || parseInt(quantityValue) < 1) {
        //     goodsErrorMessage = 'Số lượng phải lớn hơn 0!';
        //     goodsErrorField = quantityInput;
        //     goodsValid = false;
        //     // Hiển thị lỗi trực tiếp trong trường
        //     document.getElementById(`error-goods-${rowIndex}-quantity`).textContent = goodsErrorMessage;
        //     return;
        // }
        
        // // Kiểm tra đơn vị
        // if (unitValue === '') {
        //     goodsErrorMessage = 'Giá trị không được để trống!';
        //     goodsErrorField = unitInput;
        //     goodsValid = false;
        //     // Hiển thị lỗi trực tiếp trong trường
        //     document.getElementById(`error-goods-${rowIndex}-unit`).textContent = goodsErrorMessage;
        //     return;
        // }
    });
    
    if (!goodsValid) {
        handleFormError(goodsErrorMessage, goodsErrorField, 'driverAllowance');
        return false;
    }
    
    // 3. Kiểm tra các trường ở tab phương tiện & tài xế
    const vehicleId = form.querySelector('select[name="vehicle_id"]')?.value;
    const userIdField = form.querySelector('select[name="drivers[0][user_id]"]');
    const userId = userIdField ? userIdField.value : '';
    
    // Kiểm tra xem có ít nhất một khoản chi phí (deduction) được nhập hay không
    const deductionInputs = form.querySelectorAll('input[name^="deductions["]');
    const hasDeductions = deductionInputs.length > 0 && Array.from(deductionInputs).some(input => input.value.trim() !== '');
    
    // Kiểm tra xem có ít nhất một khoản chi phí cho tài xế (driver deduction) được nhập hay không
    const driverDeductionInputs = form.querySelectorAll('input[name^="drivers["][name*="[deductions]"]');
    const hasDriverDeductions = driverDeductionInputs.length > 0 && Array.from(driverDeductionInputs).some(input => input.value.trim() !== '');
    
    // Nếu vehicle_id hoặc user_id trống hoặc không có deductions, hiển thị thông báo lỗi
    if (!vehicleId || !userId) {
        let errorMessage = '';
        let errorField = null;
        
        if (!vehicleId && !userId) {
            errorMessage = 'Vui lòng chọn phương tiện và nhân sự!';
            errorField = 'select[name="vehicle_id"]';
        } else if (!vehicleId) {
            errorMessage = 'Vui lòng chọn phương tiện!';
            errorField = 'select[name="vehicle_id"]';
        } else if (!userId) {
            errorMessage = 'Vui lòng chọn nhân sự!';
            errorField = 'select[name="drivers[0][user_id]"]';
        }
        //  else if (!hasDriverDeductions) {
        //     errorMessage = 'Vui lòng nhập ít nhất một khoản phụ cấp cho tài xế!';
        //     errorField = 'input[name^="drivers["][name*="[deductions]"]';
        // }
        
        handleFormError(errorMessage, errorField, 'shipmentDetail');
        return false;
    }
    
    // Xóa tất cả các thông báo lỗi trước khi submit
    document.querySelectorAll('.text-danger').forEach(element => {
        element.textContent = '';
    });
    
    return true;
}

/**
 * Khởi tạo form tạo mới chuyến hàng
 * @param {number} initialDriverCount - Số lượng tài xế ban đầu
 */
function initShipmentForm(initialDriverCount = 1) {
    try {
        console.log('Initializing shipment form with', initialDriverCount, 'initial drivers');
        console.log('Available users:', window.users);
        
        // Thêm các dòng tài xế ban đầu nếu cần
        const personTable = document.querySelector('#personTable tbody');
        if (personTable && personTable.rows.length === 0) {
            // Chỉ thêm dòng tài xế nếu có sẵn tài xế
            if (window.users && Object.keys(window.users).length > 0) {
                for (let i = 0; i < initialDriverCount; i++) {
                    // Kiểm tra xem còn tài xế nào chưa được chọn không
                    const selectedIds = getSelectedUserIds();
                    const remainingUsers = Object.keys(window.users).filter(id => !selectedIds.includes(id));
                    
                    if (remainingUsers.length > 0) {
                        addDriverRow(personTable, window.personDeductionTypes, window.users);
                    } else {
                        console.warn('No more available users to add as drivers');
                        break;
                    }
                }
            } else {
                console.warn('No users available to add as initial drivers');
                // Hiển thị thông báo không có tài xế
                updateAddPersonButtonState();
                
                // Thêm hàng thông báo nếu không có tài xế nào
                const noDriversRow = document.createElement('tr');
                noDriversRow.id = 'no-drivers-row';
                noDriversRow.innerHTML = `
                    <td colspan="8" class="text-center">
                        <div class="alert alert-warning mb-0">
                            Không có tài xế nào khả dụng. Vui lòng thêm tài xế trước.
                        </div>
                    </td>`;
                personTable.appendChild(noDriversRow);
            }
        }
        
        // Cập nhật trạng thái nút thêm tài xế
        updateAddPersonButtonState();
        
    } catch (error) {
        console.error('Error initializing shipment form:', error);
        
        // Đảm bảo nút thêm tài xế không bị disable nếu có lỗi
        const addPersonBtn = document.getElementById('addPersonBtn');
        if (addPersonBtn) {
            addPersonBtn.disabled = false;
            addPersonBtn.classList.remove('btn-outline-secondary');
            addPersonBtn.classList.add('btn-outline-primary');
        }
    }

    // Khởi tạo giá trị ban đầu cho driverRowCount
    driverRowCount = initialDriverCount - 1;
    
    // Khởi tạo các dropdown user_id
    updateUserDropdowns();
    
    // Thêm event listener cho tất cả các trường số hiện có (ngoại trừ unit-input)
    document.querySelectorAll('input[type="number"]:not(.unit-input)').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    });
    
    // Thêm event listener cho các unit input hiện có
    document.querySelectorAll('.unit-input').forEach(input => {
        input.addEventListener('input', function() {
            // Fallback if the global function is not available
            let value = this.value;
            
            // Remove non-numeric characters and handle decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // If there's a decimal part, handle it
            if (value.includes('.')) {
                // Split into integer and decimal parts
                let parts = value.split('.');
                // If decimal part is .00, remove it completely
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0];
                } else {
                    // Otherwise keep only integer part
                    value = parts[0];
                }
            }
            
            // Allow up to 9 digits for the unit input
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            this.value = value;
        });
    });
    
    // Thêm event listener cho tất cả các dropdown user_id hiện có
    document.querySelectorAll('select[name$="[user_id]"]').forEach(select => {
        select.addEventListener('change', updateUserDropdowns);
    });
    
    // Cập nhật dropdown ban đầu
    updateUserDropdowns();
    
    // Xử lý form trước khi submit
    const form = document.getElementById('shipmentForm');
    form.addEventListener('submit', function(e) {
        // Đảm bảo tất cả các tab đều được hiển thị trước khi validate
        const tabPanes = document.querySelectorAll('.tab-pane');
        const originalDisplayStates = [];
        const originalVisibilityStates = [];
        const originalPositionStates = [];
        
        // Lưu trạng thái hiển thị ban đầu của các tab
        tabPanes.forEach(pane => {
            originalDisplayStates.push(pane.style.display);
            originalVisibilityStates.push(pane.style.visibility);
            originalPositionStates.push(pane.style.position);
            
            // Hiển thị tất cả các tab nhưng đặt vị trí ở ngoài màn hình để người dùng không thấy
            if (!pane.classList.contains('active')) {
                pane.style.display = 'block';
                pane.style.visibility = 'hidden';
                pane.style.position = 'absolute';
                pane.style.top = '-9999px';
                pane.style.left = '-9999px';
            }
        });
        
        // Chuẩn bị form trước khi submit
        prepareFormBeforeSubmit(this);
        
        // Khôi phục trạng thái hiển thị ban đầu của các tab
        tabPanes.forEach((pane, index) => {
            pane.style.display = originalDisplayStates[index];
            pane.style.visibility = originalVisibilityStates[index];
            pane.style.position = originalPositionStates[index];
        });
    });
    
    // Sử dụng nút submit để kiểm tra và hiển thị thông báo lỗi
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (validateShipmentForm(form)) {
            form.submit();
        }
    });
}

/**
 * Xử lý các lỗi form và chuyển đến tab có lỗi nếu có
 */
function handleFormErrors() {
    // Kiểm tra xem có lỗi nào không
    const hasErrors = document.querySelectorAll('.text-danger').length > 0;
    
    if (hasErrors) {
        // Tìm tab có lỗi đầu tiên
        const tabs = ['driverAllowance', 'shipmentDetail', 'goodsDetail'];
        
        for (const tabId of tabs) {
            const tabPane = document.getElementById(tabId);
            if (tabPane) {
                const errors = tabPane.querySelectorAll('.text-danger');
                const hasVisibleErrors = Array.from(errors).some(error => error.textContent.trim() !== '');
                
                if (hasVisibleErrors) {
                    // Chuyển đến tab có lỗi
                    const tabLink = document.querySelector(`a[href="#${tabId}"]`);
                    if (tabLink) {
                        const tab = new bootstrap.Tab(tabLink);
                        tab.show();
                        
                        // Focus vào trường có lỗi đầu tiên
                        setTimeout(() => {
                            const errorField = errors[0].previousElementSibling;
                            if (errorField) {
                                errorField.focus();
                                errorField.classList.add('highlight-error');
                                setTimeout(() => {
                                    errorField.classList.remove('highlight-error');
                                }, 2000);
                            }
                        }, 300);
                        
                        break;
                    }
                }
            }
        }
    }
}

/**
 * Định dạng tất cả các trường số khi trang được tải
 */
function formatAllNumericInputs() {
    // Thêm listener cho tất cả các trường số
    const numericInputs = document.querySelectorAll('input[type="number"]');
    addNumericInputListeners(numericInputs);
    
    // Định dạng giá trị ban đầu
    numericInputs.forEach(input => {
        if (input.value !== '') {
            const value = parseFloat(input.value);
            if (Number.isInteger(value)) {
                input.value = parseInt(value);
            }
        }
    });
}

/**
 * Thêm một hàng driver mới vào bảng
 * @param {HTMLElement} personTable - Bảng chứa các hàng driver
 * @param {Array} personDeductionTypes - Mảng các loại phụ cấp
 * @param {Object} users - Object chứa danh sách người dùng (id => name)
 * @returns {boolean} - Trả về true nếu thêm thành công, false nếu không thể thêm
 */
function addDriverPXRow(personTable, personDeductionTypes, users) {
    console.log(personDeductionTypes);
    // Kiểm tra xem còn người dùng khả dụng không
    if (!users || Object.keys(users).length === 0) {
        console.error('Không có tài xế nào khả dụng');
        Swal.fire({
            title: 'Lỗi',
            text: 'Không có tài xế nào khả dụng. Vui lòng thêm tài xế trước.',
            icon: 'error',
            confirmButtonText: 'Đóng'
        });
        return false;
    }
    
    const selectedIds = getSelectedUserIds(personTable, 'driverPXs');
    const totalUsers = Object.keys(users).length;
    
    console.log('Selected IDs:', selectedIds.length, 'Total Users:', totalUsers);
    
    // Nếu đã chọn hết tất cả người dùng, không cho thêm nữa
    if (selectedIds.length >= totalUsers) {
        Swal.fire({
            title: 'Không thể thêm',
            text: 'Đã sử dụng hết tất cả nhân sự có sẵn',
            icon: 'warning',
            confirmButtonText: 'Đóng'
        });
        return false;
    }
    
    // Tăng số lượng hàng
    driverRowCount++;
    
    let deductionInputs = '';
    personDeductionTypes.forEach(type => {
        deductionInputs += `<td>
            <input type="hidden" name="driverPXs[${driverRowCount}][deduction_type_ids][]" value="${type.id}">
            <input type="text" name="driverPXs[${driverRowCount}][deductions][${type.id}]" class="form-control form-control-sm deduction-input" min="0">
        </td>`;
    });
    
    const row = document.createElement('tr');
    
    // Tạo HTML cho dropdown với các option đã lọc
    let userOptionsHtml = '<option value="">Chọn nhân sự</option>';
    
    for (const id in users) {
        if (users.hasOwnProperty(id)) {
            userOptionsHtml += `<option value="${id}" ${selectedIds.includes(id) ? 'disabled style="display:none;"' : ''}>${users[id]}</option>`;
        }
    }
    
    row.innerHTML = `
        <td>
            <select name="driverPXs[${driverRowCount}][user_id]" class="form-select form-select-sm" required>
                ${userOptionsHtml}
            </select>
            <div class="text-danger" id="error-driverPXs-${driverRowCount}-user_id"></div>
        </td>
        ${deductionInputs}
        <td>
            <input type="text" name="driverPXs[${driverRowCount}][deductions][notes]" class="form-control form-control-sm">
            <div class="text-danger" id="error-driverPXs-${driverRowCount}-deductions-notes"></div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, ${driverRowCount})"><i class="ri-delete-bin-fill"></i></button>
            <input type="hidden" name="driver_rows[]" value="${driverRowCount}">
        </td>
    `;
    personTable.appendChild(row);
    
    // Thêm event listener cho dropdown mới
    const newSelect = row.querySelector(`select[name="driverPXs[${driverRowCount}][user_id]"]`);
    newSelect.addEventListener('change', updateUserDropdowns);
    
    // Thêm event listener cho các trường số mới thêm vào
    addNumericInputListeners(row.querySelectorAll('input[type="number"]'));
    
    // Apply VND formatting cho các input deduction mới thêm vào
    const newDeductionInputs = row.querySelectorAll('.deduction-input');
    newDeductionInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value;
            
            // Remove non-numeric characters and handle decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // If there's a decimal part, handle it
            if (value.includes('.')) {
                // Split into integer and decimal parts
                let parts = value.split('.');
                // If decimal part is .00, remove it completely
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0];
                } else {
                    // Otherwise keep only integer part
                    value = parts[0];
                }
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            this.value = value;
        });
    });
    
    // Kiểm tra nếu đã sử dụng hết tất cả người dùng, vô hiệu hóa nút thêm
    updateAddPersonButtonState();
    
    return true;
}