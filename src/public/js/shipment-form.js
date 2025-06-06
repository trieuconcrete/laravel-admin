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
 * @returns {Array} Mảng các user_id đã được chọn
 */
function getSelectedUserIds() {
    const selectedIds = [];
    document.querySelectorAll('select[name$="[user_id]"]').forEach(select => {
        if (select.value) {
            selectedIds.push(select.value);
        }
    });
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
    const selectedIds = getSelectedUserIds();
    
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
    
    // Cập nhật trạng thái nút thêm nhân sự
    updateAddPersonButtonState();
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
    const selectedIds = getSelectedUserIds();
    const totalUsers = Object.keys(users).length;
    
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
        ${deductionInputs}
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
    if (selectedIds.length + 1 >= totalUsers) {
        document.getElementById('addPersonBtn').disabled = true;
        document.getElementById('addPersonBtn').classList.replace('btn-outline-primary', 'btn-outline-secondary');
    }
    
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
            <input type="number" name="goods[${goodsCount}][quantity]" class="form-control form-control-sm" min="1" required>
            <div class="text-danger" id="error-goods-${goodsCount}-quantity"></div>
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][weight]" class="form-control form-control-sm" min="0">
            <div class="text-danger" id="error-goods-${goodsCount}-weight"></div>
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][unit]" class="form-control form-control-sm unit-input" required>
            <div class="text-danger" id="error-goods-${goodsCount}-unit"></div>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGoodRow(this, ${goodsCount})"><i class="ri-delete-bin-fill"></i></button>
            <input type="hidden" name="goods_rows[]" value="${goodsCount}">
        </td>
    `;
    goodsTable.appendChild(row);
    
    // Thêm event listener cho các trường số mới thêm vào
    addNumericInputListeners(row.querySelectorAll('input[type="number"]'));
    
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
                
                // Limit to 9 digits
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
    const selectedIds = getSelectedUserIds();
    const totalUsers = Object.keys(users).length;
    const addPersonBtn = document.getElementById('addPersonBtn');
    
    if (selectedIds.length >= totalUsers) {
        // Nếu đã sử dụng hết tất cả người dùng, vô hiệu hóa nút thêm
        addPersonBtn.disabled = true;
        addPersonBtn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
    } else {
        // Nếu còn người dùng khả dụng, kích hoạt nút thêm
        addPersonBtn.disabled = false;
        if (addPersonBtn.classList.contains('btn-outline-secondary')) {
            addPersonBtn.classList.replace('btn-outline-secondary', 'btn-outline-primary');
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
        const quantityInput = form.querySelector(`input[name="goods[${rowIndex}][quantity]"]`);
        const quantityValue = quantityInput ? quantityInput.value.trim() : '';
        const unitInput = form.querySelector(`input[name="goods[${rowIndex}][unit]"]`);
        const unitValue = unitInput ? unitInput.value.trim() : '';
        
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
        if (quantityValue === '' || parseInt(quantityValue) < 1) {
            goodsErrorMessage = 'Số lượng phải lớn hơn 0!';
            goodsErrorField = quantityInput;
            goodsValid = false;
            // Hiển thị lỗi trực tiếp trong trường
            document.getElementById(`error-goods-${rowIndex}-quantity`).textContent = goodsErrorMessage;
            return;
        }
        
        // Kiểm tra đơn vị
        if (unitValue === '') {
            goodsErrorMessage = 'Giá trị không được để trống!';
            goodsErrorField = unitInput;
            goodsValid = false;
            // Hiển thị lỗi trực tiếp trong trường
            document.getElementById(`error-goods-${rowIndex}-unit`).textContent = goodsErrorMessage;
            return;
        }
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
 * Khởi tạo form shipment
 * @param {number} initialDriverCount - Số lượng hàng driver ban đầu
 */
function initShipmentForm(initialDriverCount) {
    driverRowCount = initialDriverCount;
    
    // Thêm event listener cho tất cả các trường số hiện có
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
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
