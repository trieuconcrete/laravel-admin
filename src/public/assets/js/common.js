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
});