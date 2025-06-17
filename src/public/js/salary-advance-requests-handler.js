/**
 * Salary Advance Requests Handler
 * 
 * This script handles the dynamic loading of salary advance requests
 * and refreshes the list after a new request is submitted.
 */

document.addEventListener("DOMContentLoaded", function() {
    // Listen for month changes to reload the requests
    const monthSelect = document.getElementById('month-select');
    if (monthSelect) {
        monthSelect.addEventListener('change', function() {
            refreshSalaryAdvanceRequests();
        });
    }
});

/**
 * Refresh the salary advance requests list
 */
function refreshSalaryAdvanceRequests() {
    const userId = document.querySelector('input[name="user_id"]')?.value;
    if (!userId) return;
    
    const month = document.getElementById('month-select')?.value || '';
    const container = document.getElementById('salaryAdvanceRequestsContainer');
    if (!container) return;
    
    // Show loading indicator
    container.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Fetch salary advance requests
    fetch(`/admin/users/${userId}/salary-advance-requests?month=${encodeURIComponent(month)}`)
        .then(response => response.json())
        .then(data => {
            if (!data.requests || data.requests.length === 0) {
                container.innerHTML = '';
                return;
            }
            
            // Create table HTML
            let html = `
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Danh sách yêu cầu</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Loại yêu cầu</th>
                                        <th>Ngày yêu cầu</th>
                                        <th>Số tiền</th>
                                        <th>Lý do</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>`;
            
            // Add rows for each request
            data.requests.forEach(request => {
                html += `
                    <tr>
                        <td>${request.request_code}</td>
                        <td>
                            <span class="badge bg-${request.type_color}">${request.type_label}</span>
                        </td>
                        <td>${request.formatted_request_date}</td>
                        <td>${request.formatted_amount} đ</td>
                        <td>${request.reason || 'Không có'}</td>
                        <td>
                            <span class="badge bg-${request.status_color}">${request.status_label}</span>
                        </td>
                    </tr>`;
            });
            
            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
            
            // Update container
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading salary advance requests:', error);
            container.innerHTML = '<div class="alert alert-danger">Không thể tải dữ liệu đơn ứng lương</div>';
        });
}
