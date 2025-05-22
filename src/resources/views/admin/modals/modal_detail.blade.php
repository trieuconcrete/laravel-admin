<div class="modal fade" id="vehicleDetailModal" tabindex="-1" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin chi tiết phương tiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-truck fa-5x text-primary mb-3"></i>
                            <h5>{{ $vehicle->plate_number }}</h5>
                            <span class="badge bg-{{ $vehicle->getStatusBadgeClassAttribute() }}">{{ $vehicle->getStatusLabelAttribute() }}</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Biển số</p>
                                <p class="fw-bold">{{ $vehicle->plate_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Loại phương tiện</p>
                                <p class="fw-bold">{{ optional($vehicle->vehicleType)->name }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Tải trọng</p>
                                <p class="fw-bold">{{ $vehicle->capacity }} tấn</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Năm sản xuất</p>
                                <p class="fw-bold">{{ $vehicle->manufactured_year }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="nav nav-tabs" id="vehicleDetailTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#documents">Giấy tờ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#maintenance">Lịch sử bảo trì</a>
                    </li>
                </ul>
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <div class="tab-pane fade show active" id="documents">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Đăng kiểm</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>Số đăng kiểm:</td>
                                                        <td>{{ $inspection->document_number ?? null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày cấp:</td>
                                                        <td>{{ $inspection->issue_date ? $inspection->issue_date->format('Y-m-d') : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày hết hạn:</td>
                                                        <td>{{ $inspection->expiry_date ? $inspection->expiry_date->format('Y-m-d') : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Trạng thái:</td>
                                                        <td><span class="badge bg-{{ $inspection->getStatusBadgeClassAttribute() ?? 'primary' }}">{{ $inspection->getStatusLabelAttribute() ?? null }}</span></td>
                                                    </tr>
                                                    @if($inspection->document_file)
                                                        <tr>
                                                            <td>Tài liệu:</td>
                                                            <td><a href="{{ $inspection->document_file_url }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Bảo hiểm</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>Số hợp đồng:</td>
                                                        <td>{{ $insurance->document_number ?? null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày cấp:</td>
                                                        <td>{{ $insurance->issue_date ? $insurance->issue_date->format('Y-m-d') : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày hết hạn:</td>
                                                        <td>{{ $insurance->expiry_date ? $insurance->expiry_date->format('Y-m-d') : null }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Trạng thái:</td>
                                                        <td><span class="badge bg-{{ $insurance->getStatusBadgeClassAttribute() ?? 'primary' }}">{{ $insurance->getStatusLabelAttribute() ?? null }}</span></td>
                                                    </tr>
                                                    @if($insuranceDoc->document_file)
                                                        <tr>
                                                            <td>Tài liệu:</td>
                                                            <td><a href="{{ $insuranceDoc->document_file_url }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                                        </tr>
                                                    @endif
                                                    {{--  <tr>
                                                        <td>Tài liệu:</td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                                    </tr>  --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="maintenance">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Lịch sử bảo trì</h6>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm lịch bảo trì</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Loại bảo trì</th>
                                        <th>Đơn vị thực hiện</th>
                                        <th>Chi phí</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $maintenance->start_date->format('Y-m-d') ?? null }}</td>
                                        <td>{{ $maintenance->getCurrentMaintenanceTypeLabel() }}</td>
                                        <td>{{ $maintenance->service_provider }}</td>
                                        <td>{{ number_format($maintenance->cost) ?? 0 }}VNĐ</td>
                                        <td><span class="badge bg-success">{{ $maintenance->getStatusLabelAttribute() }}</span></td>
                                        <td>
                                            {{--  <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>  --}}
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Chưa có lịch sử bảo trì</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editVehicleModal">Chỉnh sửa</button>
            </div>
        </div>
    </div>
</div>
