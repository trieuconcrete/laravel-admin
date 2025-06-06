/**
 * Shipment form handling
 * Contains common functions for shipment create and edit forms
 */

// Biến để theo dõi số lượng hàng driver đã thêm vào
let driverRowCount = 0;

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
    
    // Cập nhật lại danh sách dropdown
    updateUserDropdowns();
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
}

/**
 * Thêm một hàng driver mới vào bảng
 * @param {HTMLElement} personTable - Bảng chứa các hàng driver
 * @param {Array} personDeductionTypes - Mảng các loại phụ cấp
 * @param {Object} users - Object chứa danh sách người dùng (id => name)
 */
function addDriverRow(personTable, personDeductionTypes, users) {
    // Tăng số lượng hàng
    driverRowCount++;
    
    let deductionInputs = '';
    personDeductionTypes.forEach(type => {
        deductionInputs += `<td>
            <input type="hidden" name="drivers[${driverRowCount}][deduction_type_ids][]" value="${type.id}">
            <input type="number" name="drivers[${driverRowCount}][deductions][${type.id}]" class="form-control form-control-sm numeric-input" min="0">
        </td>`;
    });
    
    const row = document.createElement('tr');
    
    // Tạo HTML cho dropdown với các option đã lọc
    let userOptionsHtml = '<option value="">Chọn nhân sự</option>';
    const selectedIds = getSelectedUserIds();
    
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
        </td>
        <td>
            <input type="text" name="goods[${goodsCount}][notes]" class="form-control form-control-sm" min="0">
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][quantity]" class="form-control form-control-sm" min="0">
        </td>
        <td>
            <input type="number" name="goods[${goodsCount}][weight]" class="form-control form-control-sm" min="0">
        </td>
        <td>
            <input type="text" name="goods[${goodsCount}][unit]" class="form-control form-control-sm">
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button></td>
    `;
    goodsTable.appendChild(row);
    
    // Thêm event listener cho các trường số mới thêm vào
    addNumericInputListeners(row.querySelectorAll('input[type="number"]'));
    
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
    const destination = form.querySelector('input[name="destination"]')?.value;
    const departureTime = form.querySelector('input[name="departure_time"]')?.value;
    const estimatedArrivalTime = form.querySelector('input[name="estimated_arrival_time"]')?.value;
    
    // Kiểm tra xem có ít nhất một hàng hóa hay không
    const goodsNameInputs = form.querySelectorAll('input[name^="goods["][name$="][name]"]');
    const hasGoods = goodsNameInputs.length > 0 && Array.from(goodsNameInputs).some(input => input.value.trim() !== '');
    
    // Kiểm tra các trường ở tab thông tin vận chuyển
    if (!customerId || !origin || !destination || !departureTime || !estimatedArrivalTime || !hasGoods) {
        let errorMessage = '';
        let errorField = null;
        let tabId = 'driverAllowance'; // ID của tab thông tin vận chuyển
        
        if (!customerId) {
            errorMessage = 'Vui lòng chọn khách hàng!';
            errorField = 'select[name="customer_id"]';
        } else if (!origin) {
            errorMessage = 'Vui lòng nhập điểm khởi hành!';
            errorField = 'input[name="origin"]';
        } else if (!destination) {
            errorMessage = 'Vui lòng nhập điểm đến!';
            errorField = 'input[name="destination"]';
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
    
    // 2. Kiểm tra các trường ở tab phương tiện & tài xế
    const vehicleId = form.querySelector('select[name="vehicle_id"]')?.value;
    const userIdField = form.querySelector('select[name="drivers[0][user_id]"]');
    const userId = userIdField ? userIdField.value : '';
    
    // Nếu vehicle_id hoặc user_id trống, hiển thị thông báo lỗi
    if (!vehicleId || !userId) {
        let errorMessage = '';
        let errorField = null;
        
        if (!vehicleId && !userId) {
            errorMessage = 'Vui lòng chọn phương tiện và nhân sự!';
            errorField = 'select[name="vehicle_id"]';
        } else if (!vehicleId) {
            errorMessage = 'Vui lòng chọn phương tiện!';
            errorField = 'select[name="vehicle_id"]';
        } else {
            errorMessage = 'Vui lòng chọn nhân sự!';
            errorField = 'select[name="drivers[0][user_id]"]';
        }
        
        handleFormError(errorMessage, errorField, 'shipmentDetail');
        return false;
    }
    
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
