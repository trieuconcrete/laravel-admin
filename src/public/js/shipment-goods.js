// Hàm format số với dấu phẩy phân cách hàng nghìn (định dạng Việt Nam)
function formatNumber(num) {
    if (isNaN(num) || num === null || num === undefined || num === '') return '';
    
    // Chuyển về số và làm tròn về số nguyên cho tiền tệ
    const number = parseFloat(num);
    if (number === 0) return '0';
    
    // Format với dấu phẩy phân cách hàng nghìn (định dạng Việt Nam)
    return Math.round(number).toLocaleString('en-US');
}

// Hàm format đặc biệt cho đơn giá (currency format)
function formatCurrency(num) {
    if (isNaN(num) || num === null || num === undefined || num === '') return '';
    
    const number = parseFloat(num);
    if (number === 0) return '0';
    
    // Format tiền tệ Việt Nam với dấu phẩy, làm tròn về số nguyên
    return Math.round(number).toLocaleString('en-US');
}

// Hàm loại bỏ format và chuyển về số
function parseNumber(str) {
    if (!str || str === '') return 0;
    
    // Loại bỏ dấu phẩy, khoảng trắng và ký tự không phải số
    const cleaned = str.toString()
        .replace(/[,\s₫]/g, '') // Loại bỏ dấu phẩy, space và ký hiệu tiền tệ
        .replace(/[^\d.-]/g, ''); // Chỉ giữ lại số, dấu chấm và dấu trừ
    
    const num = parseFloat(cleaned);
    return isNaN(num) ? 0 : num;
}

// Hàm tính toán amount cho một row
function calculateAmount(rowIndex) {
    try {
        // Lấy các input elements
        const quantityInput = document.querySelector(`input[name="goods[${rowIndex}][quantity]"]`);
        const weightInput = document.querySelector(`input[name="goods[${rowIndex}][weight]"]`);
        const unitInput = document.querySelector(`input[name="goods[${rowIndex}][unit]"]`);
        const amountInput = document.querySelector(`input[name="goods[${rowIndex}][amount]"]`);

        if (!quantityInput || !weightInput || !unitInput || !amountInput) {
            console.warn(`Không tìm thấy input elements cho row ${rowIndex}`);
            return;
        }

        // Lấy giá trị và chuyển đổi
        const quantity = parseInt(quantityInput.value) || 1; // Nếu null/empty thì dùng 1
        const weight = parseFloat(weightInput.value) || 0;
        const unit = parseNumber(unitInput.value) || 0;

        // Tính toán: amount = unit * weight * quantity
        const amount = unit * weight * quantity;

        // Cập nhật giá trị amount với format tiền tệ
        amountInput.value = formatCurrency(amount);
        
        // Thêm class để CSS có thể style
        amountInput.classList.add('formatted-currency');

        // Trigger event để các hàm khác có thể listen
        amountInput.dispatchEvent(new Event('change'));
        
        const totalElement = document.getElementById('total-amount');
        const currentTotal = totalElement ? parseNumber(totalElement.value) : 0;
        // Tính lại tổng tiền
        // if (currentTotal <= 0) {
        //     console.log('Total-amount <= 0, calculating new total...');
        //     calculateTotalAmount();
        // } else {
        //     console.log('Total-amount > 0, keeping existing total:', formatCurrency(currentTotal));
        //     // Vẫn format lại để đảm bảo display đúng
        //     if (totalElement) {
        //         totalElement.value = formatCurrency(currentTotal);
        //     }
        // }
        calculateTotalAmount();

        console.log(`Row ${rowIndex}: ${formatCurrency(unit)} * ${weight} * ${quantity} = ${formatCurrency(amount)}`);

    } catch (error) {
        console.error(`Lỗi khi tính toán amount cho row ${rowIndex}:`, error);
    }
}

// Hàm format input khi blur (rời khỏi input)
function formatInputOnBlur(input, isPrice = false) {
    const value = parseNumber(input.value);
    if (value !== 0 || input.value !== '') {
        if (isPrice) {
            input.value = formatCurrency(value);
        } else {
            input.value = formatNumber(value);
        }
    }
}

// Hàm setup event listeners cho một row
function setupRowEventListeners(rowIndex) {
    const quantityInput = document.querySelector(`input[name="goods[${rowIndex}][quantity]"]`);
    const weightInput = document.querySelector(`input[name="goods[${rowIndex}][weight]"]`);
    const unitInput = document.querySelector(`input[name="goods[${rowIndex}][unit]"]`);

    // Event listeners cho quantity
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            calculateAmount(rowIndex);
        });
        
        quantityInput.addEventListener('blur', function() {
            // Format số nguyên cho quantity
            const value = parseInt(this.value) || '';
            this.value = value === 0 ? '' : value;
            calculateAmount(rowIndex);
        });
        
        // Chỉ cho phép nhập số nguyên
        quantityInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/\d/.test(char) && e.which !== 8) {
                e.preventDefault();
            }
        });
    }

    // Event listeners cho weight
    if (weightInput) {
        weightInput.addEventListener('input', function() {
            calculateAmount(rowIndex);
        });
        
        weightInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value) && value > 0) {
                // Format với 2 chữ số thập phân
                this.value = value.toFixed(2).replace(/\.00$/, '');
            } else {
                this.value = '';
            }
            calculateAmount(rowIndex);
        });
        
        // Chỉ cho phép nhập số và dấu chấm
        weightInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[\d.]/.test(char) && e.which !== 8) {
                e.preventDefault();
            }
        });
    }

    // Event listeners cho unit (đơn giá)
    if (unitInput) {
        let formatTimeout;
        
        unitInput.addEventListener('input', function() {
            const input = this;
            
            // Clear timeout cũ
            clearTimeout(formatTimeout);
            
            // Debounce format để tránh lag khi gõ
            formatTimeout = setTimeout(function() {
                const cursorPosition = input.selectionStart;
                const oldValue = input.value;
                const rawValue = parseNumber(oldValue);
                
                if (rawValue > 0) {
                    const formattedValue = formatCurrency(rawValue);
                    
                    // Chỉ update nếu giá trị thay đổi
                    if (formattedValue !== oldValue) {
                        input.value = formattedValue;
                        
                        // Khôi phục vị trí con trỏ (tương đối)
                        const lengthDiff = formattedValue.length - oldValue.length;
                        const newPosition = Math.max(0, cursorPosition + lengthDiff);
                        
                        // Đặt con trỏ về cuối nếu thêm dấu phẩy
                        input.setSelectionRange(newPosition, newPosition);
                    }
                }
                
                calculateAmount(rowIndex);
            }, 300);
        });
        
        unitInput.addEventListener('blur', function() {
            formatInputOnBlur(this, true);
            calculateAmount(rowIndex);
        });
        
        // Chỉ cho phép nhập số và dấu phẩy
        unitInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[\d,]/.test(char) && e.which !== 8 && e.which !== 46) {
                e.preventDefault();
            }
        });
    }

    // Tính toán lần đầu nếu đã có giá trị
    calculateAmount(rowIndex);
}

// Hàm setup event listeners cho tất cả các rows hiện có
function setupAllRowEventListeners() {
    // Tìm tất cả các hidden input chứa row indexes
    const rowInputs = document.querySelectorAll('input[name="goods_rows[]"]');
    
    rowInputs.forEach(function(input) {
        const rowIndex = input.value;
        setupRowEventListeners(parseInt(rowIndex));
        
        // Format sẵn unit và amount input nếu có giá trị
        const unitInput = document.querySelector(`input[name="goods[${rowIndex}][unit]"]`);
        const amountInput = document.querySelector(`input[name="goods[${rowIndex}][amount]"]`);
        
        if (unitInput && unitInput.value) {
            const currentValue = parseNumber(unitInput.value);
            if (currentValue > 0) {
                unitInput.value = formatCurrency(currentValue);
            }
        }
        
        if (amountInput && amountInput.value) {
            const currentValue = parseNumber(amountInput.value);
            if (currentValue > 0) {
                amountInput.value = formatCurrency(currentValue);
                amountInput.classList.add('formatted-currency');
            }
        }
    });
}

// Hàm tính tổng amount của tất cả các rows
function calculateTotalAmount() {
    let total = 0;
    const rowInputs = document.querySelectorAll('input[name="goods_rows[]"]');

    rowInputs.forEach(function(input) {
        const rowIndex = input.value;
        const amountInput = document.querySelector(`input[name="goods[${rowIndex}][amount]"]`);
        if (amountInput && amountInput.value) {
            total += parseNumber(amountInput.value);
        }
    });
    
    console.log('Tổng tiền hàng hóa:', formatCurrency(total));

    // Cập nhật tổng tiền nếu có element hiển thị
    const totalElement = document.getElementById('total-amount');
    if (totalElement) {
        totalElement.value = formatCurrency(total);
        totalElement.dispatchEvent(new Event('change'));
    }
    
    return total;
}

// Hàm được gọi khi thêm row mới (cần integrate với hàm addGoodRow hiện có)
function setupNewRowEventListeners(newRowIndex) {
    setupRowEventListeners(newRowIndex);
}

// Hàm được gọi khi xóa row (cần integrate với hàm removeGoodRow hiện có)
function removeRowEventListeners(rowIndex) {
    // Tính lại tổng sau khi xóa
    setTimeout(function() {
        calculateTotalAmount();
    }, 100);
}

// Auto setup khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up shipment goods calculator...');
    
    // Setup event listeners cho tất cả rows
    setupAllRowEventListeners();
    
    // Tính toán và format tổng tiền
    calculateTotalAmount();
    
    // Format tất cả các input amount hiện có
    document.querySelectorAll('input[name*="[amount]"]').forEach(function(input) {
        if (input.value && parseNumber(input.value) > 0) {
            input.value = formatCurrency(parseNumber(input.value));
            input.classList.add('formatted-currency');
        }
    });
    
    // Format tất cả các input unit price hiện có
    document.querySelectorAll('input[name*="[unit]"]').forEach(function(input) {
        if (input.value && parseNumber(input.value) > 0) {
            input.value = formatCurrency(parseNumber(input.value));
        }
    });
});

// Hàm chuẩn bị dữ liệu trước khi submit form
function prepareFormDataForSubmit() {
    // Chuyển tất cả các trường tiền tệ về số thuần để submit
    document.querySelectorAll('input[name*="[amount]"]').forEach(function(input) {
        if (input.value) {
            const numericValue = parseNumber(input.value);
            input.value = numericValue;
        }
    });
    
    document.querySelectorAll('input[name*="[unit]"]').forEach(function(input) {
        if (input.value) {
            const numericValue = parseNumber(input.value);
            input.value = numericValue;
        }
    });
    
    document.querySelectorAll('input[name*="[weight]"]').forEach(function(input) {
        if (input.value) {
            const numericValue = parseFloat(input.value);
            input.value = isNaN(numericValue) ? 0 : numericValue;
        }
    });
}

// Hàm khôi phục format sau khi submit (nếu có lỗi validation)
function restoreFormDataFormat() {
    document.querySelectorAll('input[name*="[amount]"]').forEach(function(input) {
        if (input.value && parseNumber(input.value) > 0) {
            input.value = formatCurrency(parseNumber(input.value));
            input.classList.add('formatted-currency');
        }
    });
    
    document.querySelectorAll('input[name*="[unit]"]').forEach(function(input) {
        if (input.value && parseNumber(input.value) > 0) {
            input.value = formatCurrency(parseNumber(input.value));
        }
    });
    
    document.querySelectorAll('input[name*="[weight]"]').forEach(function(input) {
        if (input.value && parseFloat(input.value) > 0) {
            const value = parseFloat(input.value);
            input.value = value.toFixed(2).replace(/\.00$/, '');
        }
    });
}

// Hàm validate dữ liệu
function validateGoodsData() {
    let isValid = true;
    const errors = [];
    
    const rowInputs = document.querySelectorAll('input[name="goods_rows[]"]');
    
    rowInputs.forEach(function(input, index) {
        const rowIndex = input.value;
        const nameInput = document.querySelector(`input[name="goods[${rowIndex}][name]"]`);
        const weightInput = document.querySelector(`input[name="goods[${rowIndex}][weight]"]`);
        const unitInput = document.querySelector(`input[name="goods[${rowIndex}][unit]"]`);
        
        // Validate tên hàng hóa
        if (!nameInput || !nameInput.value.trim()) {
            errors.push(`Dòng ${index + 1}: Tên hàng hóa không được để trống`);
            isValid = false;
        }
        
        // Validate khối lượng
        const weight = parseFloat(weightInput?.value);
        if (!weightInput || isNaN(weight) || weight <= 0) {
            errors.push(`Dòng ${index + 1}: Khối lượng phải lớn hơn 0`);
            isValid = false;
        }
        
        // Validate đơn giá
        const unit = parseNumber(unitInput?.value);
        if (!unitInput || unit <= 0) {
            errors.push(`Dòng ${index + 1}: Đơn giá phải lớn hơn 0`);
            isValid = false;
        }
    });
    
    if (!isValid) {
        alert('Vui lòng kiểm tra lại dữ liệu:\n' + errors.join('\n'));
    }
    
    return isValid;
}

// Export functions để có thể sử dụng từ bên ngoài
window.shipmentCalculator = {
    calculateAmount,
    setupRowEventListeners: setupNewRowEventListeners,
    removeRowEventListeners,
    calculateTotalAmount,
    validateGoodsData,
    prepareFormDataForSubmit,
    restoreFormDataFormat,
    formatNumber,
    formatCurrency,
    parseNumber
};