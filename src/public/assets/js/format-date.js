function initDateInputs() {
    // Set system date format to dd/mm/YYYY
    const dateFormatPlaceholder = 'dd/mm/YYYY';
    const systemDateFormat = 'd/m/Y';

    // Function to format ddMMyyy to dd/MM/yyyy
    function formatDateString(input) {
        // Remove all non-digit characters
        let cleaned = input.replace(/\D/g, '');

        // If length is 8 (ddMMyyy), format it
        if (cleaned.length === 8) {
            const day = cleaned.substr(0, 2);
            const month = cleaned.substr(2, 2);
            const year = cleaned.substr(4, 4);
            return `${day}/${month}/${year}`;
        }

        return input;
    }

    // Function to parse YYYY-MM-DD to Date object
    function parseISODate(isoDateStr) {
        if (!isoDateStr) return null;

        const match = isoDateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) return null;

        const year = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const day = parseInt(match[3], 10);

        return new Date(year, month - 1, day);
    }

    // Function to format Date to dd/mm/yyyy
    function formatDateToDisplay(date) {
        if (!date) return '';
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // Function to validate date format and actual date
    function validateDateFormat(dateStr) {
        // Check if it matches dd/MM/yyyy pattern exactly
        const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = dateStr.match(dateRegex);

        if (!match) {
            return {
                valid: false,
                message: 'Định dạng phải là dd/mm/yyyy'
            };
        }

        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);

        // Validate basic ranges first
        if (month < 1 || month > 12) {
            return {
                valid: false,
                message: 'Tháng phải từ 01 đến 12'
            };
        }

        if (day < 1 || day > 31) {
            return {
                valid: false,
                message: 'Ngày phải từ 01 đến 31'
            };
        }

        if (year < 1000 || year > 9999) {
            return {
                valid: false,
                message: 'Năm không hợp lệ'
            };
        }

        // Check if the date is actually valid (considers leap years and days in month)
        const date = new Date(year, month - 1, day);

        // Critical check: make sure the date object represents exactly what we input
        if (date.getDate() !== day || date.getMonth() !== (month - 1) || date.getFullYear() !== year) {
            return {
                valid: false,
                message: 'Ngày không tồn tại'
            };
        }

        return {
            valid: true,
            message: '',
            date: date
        };
    }

    // Function to show/hide error message
    function showError(input, message) {
        let errorElement = input.parentNode.querySelector('.date-error-message');

        if (message) {
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'date-error-message';
                errorElement.style.color = 'red';
                errorElement.style.fontSize = '12px';
                errorElement.style.marginTop = '4px';
                input.parentNode.appendChild(errorElement);
            }
            errorElement.textContent = message;
            input.style.borderColor = 'red';
        } else {
            if (errorElement) {
                errorElement.remove();
            }
            input.style.borderColor = '';
        }
    }

    // Track all date inputs for form submission
    const dateInputs = [];

    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        // Add to tracking array
        dateInputs.push(input);

        // Lưu giá trị ban đầu và parse nó
        const originalValue = input.value;
        let isValidating = false;
        let lastValidValue = '';

        // Parse giá trị ban đầu từ YYYY-MM-DD thành Date object
        let initialDate = null;
        let initialDisplayValue = '';

        if (originalValue) {
            initialDate = parseISODate(originalValue);
            if (initialDate) {
                initialDisplayValue = formatDateToDisplay(initialDate);
                lastValidValue = initialDisplayValue;
            }
        }

        // Chuyển từ input type="date" sang input type="text" để sử dụng flatpickr
        input.type = 'text';
        input.placeholder = dateFormatPlaceholder;

        // Hiển thị giá trị ban đầu đã được format
        if (initialDisplayValue) {
            input.value = initialDisplayValue;
        }

        let flatpickrInstance = null;

        // Khởi tạo flatpickr
        flatpickrInstance = flatpickr(input, {
            allowInput: true,
            altInput: false, // Tắt altInput để tránh conflict
            dateFormat: "Y-m-d",   // Format gửi về backend: 2025-09-17
            defaultDate: initialDate, // Sử dụng Date object đã parse
            parseDate: (datestr, format) => {
                if (datestr && !isValidating) {
                    // Only parse if it's a valid date format
                    const validation = validateDateFormat(datestr);
                    if (validation.valid) {
                        return validation.date;
                    }
                }
                return null; // Return null for invalid dates to prevent auto-correction
            },
            formatDate: (date, format) => {
                // Format cho display (dd/mm/yyyy)
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            },
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0 && !isValidating) {
                    // Format date for display
                    const formattedDate = formatDateToDisplay(selectedDates[0]);
                    input.value = formattedDate;
                    lastValidValue = formattedDate;
                    showError(input, '');

                    // Set the actual value in YYYY-MM-DD format for form submission
                    const year = selectedDates[0].getFullYear();
                    const month = String(selectedDates[0].getMonth() + 1).padStart(2, '0');
                    const day = String(selectedDates[0].getDate()).padStart(2, '0');
                    input.setAttribute('data-backend-value', `${year}-${month}-${day}`);
                }
            },
            onClose: function (selectedDates, dateStr, instance) {
                // Prevent flatpickr from clearing invalid input on close
                return false;
            },
            onReady: function (selectedDates, dateStr, instance) {
                // Disable flatpickr's built-in input parsing to prevent auto-correction
                instance.config.allowInput = true;

                // Override flatpickr's blur behavior
                instance.input.addEventListener('blur', function (e) {
                    e.stopImmediatePropagation();
                }, true);

                // Set initial data-backend-value if we have initialDate
                if (initialDate) {
                    const year = initialDate.getFullYear();
                    const month = String(initialDate.getMonth() + 1).padStart(2, '0');
                    const day = String(initialDate.getDate()).padStart(2, '0');
                    input.setAttribute('data-backend-value', `${year}-${month}-${day}`);
                }
            }
        });

        // Handle input events for auto-formatting and validation
        input.addEventListener('input', function (e) {
            let value = e.target.value;
            isValidating = true;

            // Auto-format ddMMyyy to dd/MM/yyyy only if it's exactly 8 digits
            if (/^\d{8}$/.test(value)) {
                const formatted = formatDateString(value);
                if (formatted !== value) {
                    e.target.value = formatted;
                    value = formatted;
                }
            }

            // Clear previous error state initially
            showError(input, '');

            // Validate based on current input length
            if (value.length === 0) {
                // Empty input, no error
                showError(input, '');
                input.removeAttribute('data-backend-value');
            } else if (value.length > 10) {
                // Prevent input longer than 10 characters
                e.target.value = value.substring(0, 10);
                value = e.target.value;
            } else if (value.length === 10) {
                // Complete input, full validation
                const validation = validateDateFormat(value);
                if (validation.valid) {
                    lastValidValue = value;
                    flatpickrInstance.setDate(validation.date, false);
                    showError(input, '');

                    // Set the actual value in YYYY-MM-DD format for form submission
                    const year = validation.date.getFullYear();
                    const month = String(validation.date.getMonth() + 1).padStart(2, '0');
                    const day = String(validation.date.getDate()).padStart(2, '0');
                    input.setAttribute('data-backend-value', `${year}-${month}-${day}`);
                } else {
                    showError(input, validation.message);
                    input.removeAttribute('data-backend-value');
                }
            } else {
                // Partial input, check for obvious errors
                input.removeAttribute('data-backend-value');

                // Check if input contains only digits (and slashes if formatted)
                if (!/^[\d\/]*$/.test(value)) {
                    showError(input, 'Chỉ được nhập số');
                } else if (value.length >= 3) {
                    // Check day part if we have at least 2-3 characters
                    const parts = value.split('/');

                    if (parts.length >= 1) {
                        // Check day part
                        const dayStr = parts[0];
                        if (dayStr.length >= 2) {
                            const day = parseInt(dayStr, 10);
                            if (day > 31 || day === 0) {
                                showError(input, 'Ngày phải từ 01 đến 31');
                            }
                        }
                    }

                    if (parts.length >= 2) {
                        // Check month part
                        const monthStr = parts[1];
                        if (monthStr.length >= 2) {
                            const month = parseInt(monthStr, 10);
                            if (month > 12 || month === 0) {
                                showError(input, 'Tháng phải từ 01 đến 12');
                            }
                        }
                    }

                    if (parts.length >= 3) {
                        // Check year part
                        const yearStr = parts[2];
                        if (yearStr.length === 4) {
                            const year = parseInt(yearStr, 10);
                            if (year < 1000 || year > 9999) {
                                showError(input, 'Năm không hợp lệ');
                            }
                        }
                    }
                }
            }

            isValidating = false;
        });

        // Handle blur event for final validation
        input.addEventListener('blur', function (e) {
            const currentValue = e.target.value.trim();

            if (currentValue === '') {
                showError(input, '');
                lastValidValue = '';
                input.removeAttribute('data-backend-value');
                return;
            }

            // Không cho Flatpickr tự động sửa/xoá giá trị khi blur
            setTimeout(() => {
                input.value = currentValue; // luôn giữ lại input gốc

                if (currentValue.length !== 10) {
                    showError(input, 'Định dạng phải là dd/mm/yyyy');
                    input.removeAttribute('data-backend-value');
                    return;
                }

                const validation = validateDateFormat(currentValue);
                if (!validation.valid) {
                    showError(input, validation.message);
                    input.value = currentValue; // giữ nguyên text sai
                    input.removeAttribute('data-backend-value');
                } else {
                    showError(input, '');
                    lastValidValue = currentValue;
                    flatpickrInstance.setDate(validation.date, false);

                    // Set the actual value in YYYY-MM-DD format for form submission
                    const year = validation.date.getFullYear();
                    const month = String(validation.date.getMonth() + 1).padStart(2, '0');
                    const day = String(validation.date.getDate()).padStart(2, '0');
                    input.setAttribute('data-backend-value', `${year}-${month}-${day}`);
                }
            }, 0);
        });

        // Handle keydown for better UX - only allow numbers and specific keys
        input.addEventListener('keydown', function (e) {
            // Allow: backspace, delete, tab, escape, enter, home, end, left, right, up, down
            if ([8, 9, 27, 13, 35, 36, 37, 38, 39, 40, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+Z
                (e.ctrlKey === true && [65, 67, 86, 88, 90].indexOf(e.keyCode) !== -1)) {
                return;
            }

            // Only allow numbers (0-9) and forward slash
            if ((e.keyCode < 48 || e.keyCode > 57) &&
                (e.keyCode < 96 || e.keyCode > 105) &&
                e.keyCode !== 191 && e.keyCode !== 111) {
                e.preventDefault();
                return;
            }

            // Limit input length to 10 characters (dd/mm/yyyy)
            if (e.target.value.length >= 10 &&
                e.keyCode !== 8 && e.keyCode !== 46 &&
                e.target.selectionStart === e.target.selectionEnd) {
                e.preventDefault();
            }
        });

        // Prevent paste of invalid content
        input.addEventListener('paste', function (e) {
            setTimeout(() => {
                let value = e.target.value;

                // If pasted content is only digits and 8 characters, try to format
                if (/^\d{8}$/.test(value)) {
                    const formatted = formatDateString(value);
                    e.target.value = formatted;

                    // Validate the formatted date
                    const validation = validateDateFormat(formatted);
                    if (validation.valid) {
                        flatpickrInstance.setDate(validation.date, false);
                        showError(input, '');
                        lastValidValue = formatted;

                        // Set the actual value in YYYY-MM-DD format for form submission
                        const year = validation.date.getFullYear();
                        const month = String(validation.date.getMonth() + 1).padStart(2, '0');
                        const day = String(validation.date.getDate()).padStart(2, '0');
                        input.setAttribute('data-backend-value', `${year}-${month}-${day}`);
                    } else {
                        showError(input, validation.message);
                        input.removeAttribute('data-backend-value');
                    }
                } else if (value.length > 10) {
                    // Trim if too long
                    e.target.value = value.substring(0, 10);
                }
            }, 0);
        });

        // Add method to get the actual value for form submission
        input.getValue = function () {
            return this.getAttribute('data-backend-value') || '';
        };
    });

    // Intercept form submission to convert date format
    document.addEventListener('submit', function (e) {
        const form = e.target;

        const formDateInputs = dateInputs.filter(input => form.contains(input));

        const hiddenInputs = [];
        const removedNames = [];

        formDateInputs.forEach(input => {
            const backendValue = input.getAttribute('data-backend-value');

            if (backendValue && input.name) {
                removedNames.push({ input: input, name: input.name });

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = backendValue;

                input.removeAttribute('name');

                form.appendChild(hiddenInput);
                hiddenInputs.push(hiddenInput);
            }
        });

        form._dateHiddenInputs = hiddenInputs;
        form._removedDateNames = removedNames;

        setTimeout(() => {
            removedNames.forEach(item => {
                item.input.setAttribute('name', item.name);
            });
        }, 0);
    });


    // Cleanup function (optional, for SPA or dynamic forms)
    window.cleanupDateInputs = function (form) {
        if (form._dateHiddenInputs) {
            form._dateHiddenInputs.forEach(input => input.remove());
            form._dateHiddenInputs = null;
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    initDateInputs();
});
