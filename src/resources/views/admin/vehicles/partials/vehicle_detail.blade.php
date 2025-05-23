<div class="row">
    <div class="col-md-4 text-center mb-3">
        <div class="border rounded p-3">
            <i class="fas fa-truck fa-5x text-primary mb-3"></i>
            <h5>{{ $vehicle->vehicleType->name ?? 'Không xác định' }} - {{ $vehicle->plate_number }}</h5>
            <span class="badge bg-{{ $vehicle->status_badge_class }}">{{ $vehicle->status_label }}</span>
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
                <p class="fw-bold">{{ optional($vehicle->vehicleType)->name ?? 'Không xác định' }}</p>
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
            @php
                $inspectionDoc = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSPECTION);
                $insuranceDoc = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSURANCE);
            @endphp
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Đăng kiểm</h6>
                        @if($inspectionDoc)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Số đăng kiểm:</td>
                                            <td>{{ $inspectionDoc->document_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ngày cấp:</td>
                                            <td>{{ $inspectionDoc->issue_date ? $inspectionDoc->issue_date->format('d/m/Y') : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ngày hết hạn:</td>
                                            <td>{{ $inspectionDoc->expiry_date ? $inspectionDoc->expiry_date->format('d/m/Y') : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>Trạng thái:</td>
                                            <td><span class="badge bg-{{ $inspectionDoc->status_badge_class }}">{{ $inspectionDoc->status_label }}</span></td>
                                        </tr>
                                        @if($inspectionDoc->document_file)
                                            <tr>
                                                <td>Tài liệu:</td>
                                                <td><a href="{{ $inspectionDoc->document_file_url }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">Chưa có thông tin đăng kiểm</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Bảo hiểm</h6>
                        @if($insuranceDoc)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Số hợp đồng:</td>
                                            <td>{{ $insuranceDoc->document_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ngày cấp:</td>
                                            <td>{{ $insuranceDoc->issue_date ? $insuranceDoc->issue_date->format('d/m/Y') : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ngày hết hạn:</td>
                                            <td>{{ $insuranceDoc->expiry_date ? $insuranceDoc->expiry_date->format('d/m/Y') : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>Trạng thái:</td>
                                            <td><span class="badge bg-{{ $insuranceDoc->status_badge_class }}">{{ $insuranceDoc->status_label }}</span></td>
                                        </tr>
                                        @if($insuranceDoc->document_file)
                                            <tr>
                                                <td>Tài liệu:</td>
                                                <td><a href="{{ $insuranceDoc->document_file_url }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">Chưa có thông tin bảo hiểm</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="maintenance">
        <div class="d-flex justify-content-between mb-3">
            <h6>Lịch sử bảo trì</h6>
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
                    @forelse($vehicle->maintenanceRecords as $record)
                        <tr>
                            <td>{{ $record->start_date ? $record->start_date->format('d/m/Y') : null }}</td>
                            <td>{{ $record->getCurrentMaintenanceTypeLabel() ?? null }}</td>
                            <td>{{ $record->service_provider }}</td>
                            <td>{{ number_format($record->cost) ?? 0 }} VNĐ</td>
                            <td><span class="badge bg-{{ $record->getStatusBadgeClassAttribute() }}">
                                {{ $record->getStatusLabelAttribute() }}
                            </span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary maintenance-detail-btn" data-record-id="{{ $record->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
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
