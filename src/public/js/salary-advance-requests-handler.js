/**
 * Salary Advance Requests Handler
 * 
 * This script handles the dynamic loading of salary advance requests
 * and refreshes the list after a new request is submitted.
 */

// Add this function to handle edit button click
function setupEditModal() {
    // Handle edit button click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-edit-request')) {
            e.preventDefault();
            const requestData = JSON.parse(e.target.dataset.request);
            openEditModal(requestData);
        }
    });

    // Handle form submission
    const form = document.getElementById('editSalaryAdvanceRequestForm');
    if (form) {
        form.addEventListener('submit', handleUpdateRequest);
    }
}

function openEditModal(requestData) {
    const modal = new bootstrap.Modal(document.getElementById('editSalaryAdvanceModal'));
    const form = document.getElementById('editSalaryAdvanceRequestForm');
    
    // Set the form action with the correct URL
    const userId = document.querySelector('input[name="user_id"]').value;
    form.action = `/admin/users/${userId}/salary-advance-requests/${requestData.id}`;
    
    // Populate form fields
    form.querySelector('input[name="user_id"]').value = requestData.user_id;
    form.querySelector('input[name="request_id"]').value = requestData.id;
    form.querySelector('input[name="amount"]').value = requestData.formatted_amount;
    form.querySelector('input[name="advance_month"]').value = requestData.advance_month; // Format date
    form.querySelector('select[name="type"]').value = requestData.type || requestData.type_key;
    form.querySelector('select[name="status"]').value = requestData.status || requestData.status_key;
    form.querySelector('textarea[name="reason"]').value = requestData.reason || '';
    
    // Show modal
    modal.show();
}

async function handleUpdateRequest(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const errorDiv = document.getElementById('editSalaryAdvanceRequestError');
    
    try {
        submitBtn.disabled = true;
        errorDiv.style.display = 'none';
        
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Có lỗi xảy ra khi cập nhật yêu cầu');
        }
        
        // Show success message with Swal
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: data.message || 'Cập nhật yêu cầu thành công',
            confirmButtonText: 'Đóng',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        }).then(() => {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editSalaryAdvanceModal'));
            modal.hide();
            
            // Reset form
            form.reset();
            
            // Refresh the requests list
            refreshSalaryAdvanceRequests();
        });
    } catch (error) {
        console.error('Error updating request:', error);
        errorDiv.textContent = error.message || 'Có lỗi xảy ra khi cập nhật yêu cầu';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
    }
}

// Update the refreshSalaryAdvanceRequests function to include edit button
function refreshSalaryAdvanceRequests() {
    console.log('Refreshing salary advance requests...');
    const userId = document.querySelector('input[name="user_id"]')?.value;
    if (!userId) return;
    
    const month = document.getElementById('salaryMonth')?.value || '';
    const container = document.getElementById('salaryAdvanceRequestsContainer');
    if (!container) return;
    
    fetch(`/admin/users/${userId}/salary-advance-requests?month=${encodeURIComponent(month)}`)
        .then(response => response.json())
        .then(data => {
            // Create table HTML
            let html = `
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Danh sách yêu cầu</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mã yêu cầu</th>
                                        <th>Loại yêu cầu</th>
                                        <th>Ngày yêu cầu</th>
                                        <th>Số tiền</th>
                                        <th>Lý do</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>`;
            
            if (!data.requests || data.requests.length === 0) {
                html += `
                    <tr>
                        <td colspan="7" class="text-center">Không có dữ liệu cho tháng này!</td>
                    </tr>`;
            } else {
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
                            <td>${request.reason || '-'}</td>
                            <td><span class="badge bg-${request.status_color}">${request.status_label}</span></td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm btn-edit-request" 
                                        data-request='${JSON.stringify(request).replace(/'/g, "&apos;")}'>
                                    Sửa
                                </button>
                            </td>
                        </tr>`;
                });
            }
            
            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching salary advance requests:', error);
            container.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải danh sách yêu cầu</div>';
        });
}

// Initialize the modal when the page loads
document.addEventListener("DOMContentLoaded", function() {
    setupEditModal();
    
    // Rest of your existing code...
    const monthSelect = document.getElementById('salaryMonth');
    if (monthSelect) {
        monthSelect.addEventListener('change', function() {
            refreshSalaryAdvanceRequests();
        });
    }
});
