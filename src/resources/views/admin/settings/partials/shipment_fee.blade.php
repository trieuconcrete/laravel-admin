<!-- Filters -->
<form method="GET" action="{{ route('admin.settings.index', 'shipment-fee') }}#shipment-fee" class="mb-4">
    <input type="hidden" name="group" id="settingGroup" value="shipment-fee">
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
                <i class="fas fa-search"></i> Tìm kiếm
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
                            <i class="ri-eye-line"></i>
                        </a>
                        <a href="{{ route('admin.shipment-deduction-types.edit', $item) }}" class="btn btn-sm btn-warning">
                            <i class=" ri-edit-line"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}">
                            <i class="ri-delete-bin-line"></i>
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