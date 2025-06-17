@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-route-fill fs-1"></i> Quản lý chuyến hàng</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm chuyến hàng 
                                    </a>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>

            <!-- Dashboard Cards -->
            @php
                // Đếm tổng số chuyến hàng
                $totalShipments = $shipments->total();
                
                // Đếm số chuyến hàng đang vận chuyển
                $inTransitCount = App\Models\Shipment::where('status', App\Models\Shipment::STATUS_IN_TRANSIT)->count();
                
                // Đếm số chuyến hàng sắp khởi hành (đã xác nhận nhưng chưa vận chuyển)
                $confirmedCount = App\Models\Shipment::where('status', App\Models\Shipment::STATUS_CONFIRMED)->count();
                
                // Đếm số chuyến hàng hoàn thành trong tháng hiện tại
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $completedThisMonth = App\Models\Shipment::where('status', App\Models\Shipment::STATUS_COMPLETED)
                    ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                    ->count();
            @endphp
            
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #4e73df;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Tổng chuyến hàng</div>
                                    <h4 class="mt-2">{{ $totalShipments }}</h4>
                                </div>
                                <div>
                                    <i class="ri-route-fill fs-1 text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #1cc88a;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Đang hoạt động</div>
                                    <h4 class="mt-2">{{ $inTransitCount }}</h4>
                                </div>
                                <div>
                                    <i class="ri-truck-fill fs-1 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #f6c23e;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Sắp khởi hành</div>
                                    <h4 class="mt-2">{{ $confirmedCount }}</h4>
                                </div>
                                <div>
                                    <i class="ri-time-fill fs-1 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #36b9cc;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Hoàn thành (tháng)</div>
                                    <h4 class="mt-2">{{ $completedThisMonth }}</h4>
                                </div>
                                <div>
                                    <i class="ri-checkbox-circle-fill fs-1 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.shipments.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    @foreach($shipmentStatus as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text">Từ</span>
                                    <input type="date" class="form-control" id="startDateFilter" name="departure_time" value="@formatDateForInput(request('departure_time'))">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text">Đến</span>
                                    <input type="date" class="form-control" id="endDateFilter" name="estimated_arrival_time" value="@formatDateForInput(request('estimated_arrival_time'))">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm mã chuyến, tuyến, tài xế..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100" type="submit">
                                    <i class="fas fa-filter me-2"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- List View -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Thao tác</th>
                                    <th>Mã chuyến</th>
                                    <th>Tuyến đường</th>
                                    <th>Tài xế</th>
                                    <th>Phương tiện</th>
                                    <th>Thời gian</th>
                                    <th>Ngày tạo</th>
                                    <th>Người tạo</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipments as $shipment)
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn btn-sm btn-outline-primary ">Chi tiết</a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-shipment-btn"
                                                    data-shipment-id="{{ $shipment->id }}">
                                                Xóa
                                            </button>
                                            
                                            <form action="{{ route('admin.shipments.destroy', $shipment) }}"
                                                method="POST"
                                                class="delete-shipment-form"
                                                id="delete-form-{{ $shipment->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                    <td><strong>{{ $shipment->shipment_code }}</strong></td>
                                    <td>{{ $shipment->origin }} - {{ $shipment->destination }}</td>
                                    <td>{{ $shipment->getDriverFromShipmentDeductions()->full_name ?? '' }}</td>
                                    <td>{{ $shipment->vehicle ? $shipment->vehicle->plate_number : '' }}</td>
                                    <td>
                                        <div>KH: @formatDate($shipment->departure_time)</div>
                                        <div>DK: @formatDate($shipment->estimated_arrival_time)</div>
                                    </td>
                                    <td>@formatDate($shipment->created_at)</td>
                                    <td>{{ $shipment->creator->full_name ?? null }}</td>
                                    <td><span class="badge {{ $shipment->statusBadgeClass }}">{{ $shipment->status_label }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {!! $shipments->withQueryString()->links() !!}
                </div>
            </div>
        </div> <!-- end col -->
    </div>
</div>
<!-- container-fluid -->
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-shipment-btn').click(function (e) {
            e.preventDefault();
    
            const shipmentId = $(this).data('shipment-id');
            const form = $('#delete-form-' + shipmentId);
    
            Swal.fire({
                title: 'Bạn chắc chắn muốn xóa?',
                // text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    </script>
@endpush