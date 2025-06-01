@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Custom toastr styles */
    .toast-success {
        background-color: #10b981 !important;
    }
    .toast-error {
        background-color: #ef4444 !important;
    }
    .toast-warning {
        background-color: #f59e0b !important;
    }
    .toast-info {
        background-color: #3b82f6 !important;
    }
    
    /* Form validation styles */
    .is-invalid {
        border-color: #ef4444 !important;
    }
    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Custom SweetAlert2 styles */
    .swal2-popup {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    .swal2-confirm {
        background-color: #3b82f6 !important;
        box-shadow: none !important;
    }
    .swal2-cancel {
        background-color: #6b7280 !important;
        box-shadow: none !important;
    }
    .swal2-deny {
        background-color: #ef4444 !important;
    }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.contacts.index') }}" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" 
                               id="search-input" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Tìm kiếm theo tên, email, chủ đề..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <select name="status" id="status-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã phản hồi</option>
                        <option value="spam" {{ request('status') == 'spam' ? 'selected' : '' }}>Spam</option>
                    </select>
                </div>
                
                <!-- Date Filter -->
                <div>
                    <select name="date_filter" id="date-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Tất cả thời gian</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>30 ngày qua</option>
                    </select>
                </div>
            </div>
        </form>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div class="bg-blue-50 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-600 font-medium">Tin mới</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $stats['new'] }}</p>
                    </div>
                    <i class="fas fa-envelope text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-yellow-600 font-medium">Đã đọc</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $stats['read'] }}</p>
                    </div>
                    <i class="fas fa-eye text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 font-medium">Đã reply</p>
                        <p class="text-2xl font-bold text-green-700">{{ $stats['replied'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-red-50 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-red-600 font-medium">Spam</p>
                        <p class="text-2xl font-bold text-red-700">{{ $stats['spam'] }}</p>
                    </div>
                    <i class="fas fa-ban text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contacts Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người gửi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chủ đề</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tin nhắn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="contacts-tbody">
                    @forelse($contacts as $contact)
                    <tr class="contact-row" data-id="{{ $contact->id }}">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="contact-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $contact->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 {{ $contact->isNew() ? 'font-semibold' : '' }}">
                                {{ $contact->subject }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                {{ $contact->message_excerpt }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($contact->status == 'new')
                                <span class="status-badge px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700 flex items-center w-fit">
                                    <span class="unread-indicator w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                                    Mới
                                </span>
                            @elseif($contact->status == 'read')
                                <span class="status-badge px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                    Đã đọc
                                </span>
                            @elseif($contact->status == 'replied')
                                <span class="status-badge px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    Đã phản hồi
                                </span>
                            @elseif($contact->status == 'spam')
                                <span class="status-badge px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                    Spam
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $contact->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="view-btn p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                        data-id="{{ $contact->id }}" 
                                        title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="reply-btn p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" 
                                        data-id="{{ $contact->id }}"
                                        title="Phản hồi">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="spam-btn p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                        data-id="{{ $contact->id }}"
                                        title="Đánh dấu spam">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Không có liên hệ nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($contacts->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Hiển thị <span class="font-medium">{{ $contacts->firstItem() }}</span> 
                    đến <span class="font-medium">{{ $contacts->lastItem() }}</span> 
                    trong tổng số <span class="font-medium">{{ $contacts->total() }}</span> liên hệ
                </div>
                <div>
                    {{ $contacts->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Bulk Actions -->
    <div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg px-6 py-3 hidden">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600"><span id="selected-count">0</span> mục đã chọn</span>
            <button id="bulk-read" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-eye mr-2"></i>Đánh dấu đã đọc
            </button>
            <button id="bulk-spam" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-ban mr-2"></i>Đánh dấu spam
            </button>
        </div>
    </div>
</main>

<!-- Contact Detail Modal -->
<div id="contact-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white shadow-xl overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Chi tiết liên hệ</h2>
                <button onclick="closeModal()" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="modal-content">
                <!-- Contact details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 loading-overlay z-40 hidden">
    <div class="flex items-center justify-center h-full">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
    // CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    
    // Auto submit form on filter change
    $('#status-filter, #date-filter').change(function() {
        $('#filter-form').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    $('#search-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $('#filter-form').submit();
        }, 500);
    });
    
    // Select all checkbox
    $('#select-all').change(function() {
        $('.contact-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });
    
    // Individual checkbox
    $(document).on('change', '.contact-checkbox', function() {
        updateBulkActions();
    });
    
    // Update bulk actions
    function updateBulkActions() {
        const checkedCount = $('.contact-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulk-actions').removeClass('hidden');
            $('#selected-count').text(checkedCount);
        } else {
            $('#bulk-actions').addClass('hidden');
        }
    }
    
    // View contact details
    $(document).on('click', '.view-btn', function() {
        const id = $(this).data('id');
        showContactDetails(id);
    });
    
    // Reply button (quick access)
    $(document).on('click', '.reply-btn', function() {
        const id = $(this).data('id');
        showContactDetails(id);
    });
    
    // Show contact details modal
    function showContactDetails(id) {
        $('#loading-overlay').removeClass('hidden');
        
        $.get(`/admin/contacts/${id}`, function(contact) {
            $('#modal-content').html(`
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Thông tin người gửi</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 w-24">Họ tên:</span>
                                <span class="text-sm font-medium text-gray-900">${contact.name}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 w-24">Email:</span>
                                <span class="text-sm font-medium text-gray-900">${contact.email}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 w-24">IP:</span>
                                <span class="text-sm font-medium text-gray-900">${contact.ip_address || 'N/A'}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 w-24">Thời gian:</span>
                                <span class="text-sm font-medium text-gray-900">${new Date(contact.created_at).toLocaleString('vi-VN')}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Chủ đề</h3>
                        <p class="text-gray-900">${contact.subject}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Nội dung</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">${contact.message}</p>
                    </div>
                    
                    ${contact.admin_notes ? `
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Ghi chú admin</h3>
                        <p class="text-gray-600 whitespace-pre-wrap bg-yellow-50 p-3 rounded">${contact.admin_notes}</p>
                    </div>
                    ` : ''}
                    
                    ${contact.status != 'replied' ? `
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Phản hồi nhanh</h3>
                        <form id="reply-form" data-contact-id="${contact.id}">
                            <div class="form-group">
                                <textarea 
                                    id="reply-message" 
                                    name="message"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" 
                                    rows="4" 
                                    placeholder="Nhập nội dung phản hồi (tối thiểu 10 ký tự)..."
                                    minlength="10"
                                    maxlength="5000"
                                    required></textarea>
                                <div class="invalid-feedback"></div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-gray-500">Tối thiểu 10 ký tự</span>
                                    <span class="text-xs text-gray-500"><span id="char-count">0</span>/5000</span>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-end space-x-3">
                                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    Hủy
                                </button>
                                <button type="submit" id="send-reply-btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-paper-plane mr-2"></i>Gửi phản hồi
                                </button>
                            </div>
                        </form>
                    </div>
                    ` : '<div class="mt-4 p-4 bg-green-50 rounded-lg text-green-700 text-sm"><i class="fas fa-check-circle mr-2"></i>Liên hệ này đã được phản hồi</div>'}
                </div>
            `);
            
            $('#contact-modal').removeClass('hidden');
            $('#loading-overlay').addClass('hidden');
            
            // Initialize reply form if exists
            if ($('#reply-form').length) {
                initializeReplyForm();
            }
        }).fail(function() {
            $('#loading-overlay').addClass('hidden');
            toastr.error('Có lỗi xảy ra khi tải thông tin liên hệ');
        });
    }
    
    // Initialize reply form
    function initializeReplyForm() {
        const textarea = $('#reply-message');
        const charCount = $('#char-count');
        const form = $('#reply-form');
        
        // Character counter
        textarea.on('input', function() {
            const length = $(this).val().length;
            charCount.text(length);
            
            // Remove error state when typing
            if (length >= 10) {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('');
            }
        });
        
        // Form submission
        form.on('submit', function(e) {
            e.preventDefault();
            
            const message = textarea.val().trim();
            const contactId = $(this).data('contact-id');
            
            // Client-side validation
            if (!message) {
                showFieldError(textarea, 'Vui lòng nhập nội dung phản hồi');
                return;
            }
            
            if (message.length < 10) {
                showFieldError(textarea, 'Nội dung phản hồi phải có ít nhất 10 ký tự');
                return;
            }
            
            if (message.length > 5000) {
                showFieldError(textarea, 'Nội dung phản hồi không được vượt quá 5000 ký tự');
                return;
            }
            
            // Confirm before sending
            Swal.fire({
                title: 'Xác nhận gửi phản hồi',
                html: `
                    <div class="text-left">
                        <p class="mb-2">Bạn sắp gửi phản hồi đến: <strong>${$('#modal-content .text-gray-900').eq(1).text()}</strong></p>
                        <div class="bg-gray-100 p-3 rounded text-sm">
                            ${message.substring(0, 150)}${message.length > 150 ? '...' : ''}
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane mr-2"></i>Gửi phản hồi',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sendReplyMessage(contactId, message);
                }
            });
        });
    }
    
    // Send reply message
    function sendReplyMessage(contactId, message) {
        // Show loading state
        const btn = $('#send-reply-btn');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...');
        
        $.ajax({
            url: `/admin/contacts/${contactId}/reply`,
            method: 'POST',
            data: { message: message },
            success: function(response) {
                if (response.success) {
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Thành công!',
                    //     text: response.message,
                    //     timer: 2000,
                    //     showConfirmButton: false
                    // });
                    // closeModal();
                    
                    // // Update UI to reflect replied status
                    // const row = $(`.contact-row[data-id="${contactId}"]`);
                    // row.find('.status-badge').replaceWith(`
                    //     <span class="status-badge px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                    //         Đã phản hồi
                    //     </span>
                    // `);

                    toastr.success(response.message);
                    
                    // Reload after delay
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message || 'Có lỗi xảy ra');
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                // Handle validation errors
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.message) {
                        showFieldError($('#reply-message'), errors.message[0]);
                    }
                } else {
                    toastr.error('Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại.');
                }
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    }
    
    // Show field error
    function showFieldError(field, message) {
        field.addClass('is-invalid');
        field.siblings('.invalid-feedback').text(message);
        field.focus();
    }
    
    // Close modal
    window.closeModal = function() {
        $('#contact-modal').addClass('hidden');
        $('#modal-content').empty();
    }
    
    // Update status - Spam
    $(document).on('click', '.spam-btn', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const contactName = row.find('.text-gray-900').first().text();
        
        Swal.fire({
            title: 'Đánh dấu là spam?',
            html: `Bạn có chắc muốn đánh dấu tin nhắn từ <strong>${contactName}</strong> là spam?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-ban mr-2"></i>Đánh dấu spam',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#ef4444',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 'spam');
            }
        });
    });
    
    function updateStatus(id, status) {
        $.ajax({
            url: `/admin/contacts/${id}/status`,
            method: 'PATCH',
            data: { status: status },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
            }
        });
    }
    
    // Bulk actions
    $('#bulk-read').click(function() {
        const count = $('.contact-checkbox:checked').length;
        
        Swal.fire({
            title: 'Đánh dấu đã đọc?',
            text: `Bạn sắp đánh dấu ${count} tin nhắn là đã đọc.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-eye mr-2"></i>Đánh dấu đã đọc',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                bulkUpdate('read');
            }
        });
    });
    
    $('#bulk-spam').click(function() {
        const count = $('.contact-checkbox:checked').length;
        
        Swal.fire({
            title: 'Xác nhận đánh dấu spam?',
            html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-yellow-500 mb-4"></i>
                    <p>Bạn sắp đánh dấu <strong>${count}</strong> tin nhắn là spam.</p>
                    <p class="text-sm text-gray-600 mt-2">Hành động này có thể được hoàn tác.</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-ban mr-2"></i>Đánh dấu spam',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#ef4444',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                bulkUpdate('spam');
            }
        });
    });
    
    function bulkUpdate(action) {
        const ids = $('.contact-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (ids.length === 0) {
            toastr.warning('Vui lòng chọn ít nhất một mục');
            return;
        }
        
        $.post('/admin/contacts/bulk-update', {
            ids: ids,
            action: action
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }).fail(function() {
            toastr.error('Có lỗi xảy ra khi xử lý yêu cầu');
        });
    }
    
    // Delete contact (if needed)
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const contactName = row.find('.text-gray-900').first().text();
        
        Swal.fire({
            title: 'Xóa liên hệ?',
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-5xl text-red-500 mb-4"></i>
                    <p>Bạn có chắc muốn xóa liên hệ từ <strong>${contactName}</strong>?</p>
                    <p class="text-sm text-red-600 mt-2">Hành động này không thể hoàn tác!</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#ef4444',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                deleteContact(id);
            }
        });
    });
    
    function deleteContact(id) {
        $.ajax({
            url: `/admin/contacts/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Remove row with animation
                    $(`.contact-row[data-id="${id}"]`).fadeOut(400, function() {
                        $(this).remove();
                        updateBulkActions();
                    });
                }
            },
            error: function() {
                toastr.error('Không thể xóa liên hệ này');
            }
        });
    }
    
    // Refresh button
    $('#refresh-btn').click(function() {
        $(this).find('i').addClass('fa-spin');
        location.reload();
    });
    
    // Show success message if exists in session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
});
</script>
@endpush