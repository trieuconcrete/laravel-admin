@extends('admin.layout')
@section('title', 'Quản lý chi phí và phụ cấp chuyến xe')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title">Quản lý chi phí chuyến xe</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.shipment-deduction-types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm mới
                            </a>
                            <button type="button" class="btn btn-danger ms-2" id="bulkDeleteBtn" style="display: none;">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.shipment-deduction-types.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">Tất cả loại</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Chi phí khác hàng thanh toán</option>
                                    <option value="driver" {{ request('type') == 'driver' ? 'selected' : '' }}>Chi phí cho Tài</option>
                                    <option value="bus_driver" {{ request('type') == 'bus_driver' ? 'selected' : '' }}>Chi phí cho Lơ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Notes</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @forelse($deductionTypes as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}">
                                    </td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->type == 'expense' ? 'info' : ($item->type == 'driver' ? 'warning' : 'success') }}">
                                            {{ $item->type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'danger' }}">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->order }}</span>
                                    </td>
                                    <td>{{ Str::limit($item->notes, 50) }}</td>
                                    <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $item->updated_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.shipment-deduction-types.show', $item) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.shipment-deduction-types.edit', $item) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No data found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $deductionTypes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteBtn();
    });

    // Individual checkbox
    $('.item-checkbox').on('change', function() {
        toggleBulkDeleteBtn();
    });

    function toggleBulkDeleteBtn() {
        if ($('.item-checkbox:checked').length > 0) {
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }

    // Delete single item
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/shipment-deduction-types/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = $('.item-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Warning', 'Please select at least one item.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} item(s)!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.shipment-deduction-types.bulk-delete") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    // Make table sortable
    $("#sortable").sortable({
        update: function(event, ui) {
            const items = [];
            $('#sortable tr').each(function(index) {
                items.push({
                    id: $(this).data('id'),
                    order: index + 1
                });
            });

            $.ajax({
                url: '{{ route("admin.shipment-deduction-types.update-order") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    items: items
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('Failed to update order');
                }
            });
        }
    });
});
</script>
@endsection