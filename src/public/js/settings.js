/**
 * JavaScript cho trang quản lý cài đặt
 */
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý các toggle switch hiển thị/ẩn các trường phụ thuộc
    const toggleSwitches = document.querySelectorAll('[data-toggle-target]');
    
    toggleSwitches.forEach(function(toggle) {
        // Thiết lập trạng thái ban đầu
        const targetSelector = toggle.getAttribute('data-toggle-target');
        const targetElement = document.querySelector(targetSelector);
        
        if (targetElement) {
            targetElement.classList.toggle('d-none', !toggle.checked);
            
            // Thêm sự kiện change
            toggle.addEventListener('change', function() {
                targetElement.classList.toggle('d-none', !this.checked);
            });
        }
    });
    
    // Xử lý chuyển tab và lưu tab đang active vào localStorage
    const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    
    // Khôi phục tab đã chọn trước đó
    const activeTab = localStorage.getItem('settings_active_tab');
    if (activeTab) {
        const tabToActivate = document.querySelector(`.nav-link[href="${activeTab}"]`);
        if (tabToActivate) {
            const tab = new bootstrap.Tab(tabToActivate);
            tab.show();
        }
    }
    
    // Lưu tab đang active khi chuyển tab
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(event) {
            localStorage.setItem('settings_active_tab', event.target.getAttribute('href'));
        });
    });
    
    // Xử lý xác nhận trước khi reset về mặc định
    const resetForm = document.getElementById('reset-settings-form');
    if (resetForm) {
        resetForm.addEventListener('submit', function(event) {
            if (!confirm('Bạn có chắc chắn muốn đặt lại tất cả cài đặt về giá trị mặc định? Hành động này không thể hoàn tác.')) {
                event.preventDefault();
            }
        });
    }
    
    // Xử lý xác nhận trước khi xóa cache
    const clearCacheForm = document.getElementById('clear-cache-form');
    if (clearCacheForm) {
        clearCacheForm.addEventListener('submit', function(event) {
            if (!confirm('Bạn có chắc chắn muốn xóa cache cài đặt? Điều này có thể ảnh hưởng tạm thời đến hiệu suất.')) {
                event.preventDefault();
            }
        });
    }
});
