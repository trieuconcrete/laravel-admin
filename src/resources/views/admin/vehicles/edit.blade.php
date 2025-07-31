@extends('admin.layout')
@section('title', 'Sửa phương tiện')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Chỉnh sửa phương tiện</h4>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @php
                                    $inspectionDoc = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSPECTION);
                                    $insuranceDoc = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSURANCE);
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Biển số xe <span class="text-danger">*</span></label>
                                        <input type="text" name="plate_number" id="plate_number" class="form-control" placeholder="Nhập biển số xe" value="{{ old('address', $vehicle->plate_number) }}">
                                        @error('plate_number')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Loại phương tiện <span class="text-danger">*</span></label>
                                        <select class="form-select" name="vehicle_type_id" id="vehicle_type_id">
                                            <option value="">Chọn loại phương tiện</option>
                                            @foreach ($vehicleTypes as $key => $val)
                                                <option value="{{ $key }}"
                                                    {{ old('vehicle_type_id', $vehicle->vehicle_type_id) == $key ? 'selected' : '' }}>
                                                    {{ $val }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_type_id')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tải trọng (tấn)</label>
                                        <input type="number" step="0.1" class="form-control" name="capacity" id="capacity" value="{{ old('address', $vehicle->capacity) }}">
                                        @error('capacity')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Năm sản xuất</label>
                                        <input type="number" class="form-control" name="manufactured_year" id="manufactured_year" value="{{ old('address', $vehicle->manufactured_year) }}">
                                        @error('manufactured_year')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <select class="form-select" name="status">
                                            @foreach ($vehicleStatuses as $val => $label)
                                                <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tài xế </label>
                                        <select class="form-select" name="driver_id">
                                            @foreach ($drivers as $key => $driver)
                                                <option value="{{ $key }}">{{ $driver }}</option>
                                            @endforeach
                                        </select>
                                        @error('driver_id')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <h6>Thông tin đăng kiểm</h6>
                                <input type="text" class="form-control" name="documents[0][document_type]" value="{{ \App\Models\VehicleDocument::TYPE_INSPECTION }}" hidden>
                                <input type="text" class="form-control" name="documents[0][document_id]" value="{{ $inspectionDoc->document_id ?? null }}" hidden>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Số giấy đăng kiểm </label>
                                        <input type="text" class="form-control" name="documents[0][document_number]" value="{{ old('documents[0][document_number]', $inspectionDoc->document_number ?? null) }}">
                                        @error('documents[0][document_number]')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày hết hạn</label>
                                        <input
                                            type="date" 
                                            class="form-control" 
                                            name="documents[0][expiry_date]" 
                                            placeholder="{{ \App\Helpers\DateHelper::getDateFormatPlaceholder() }}"
                                            value="{{ old('documents.0.expiry_date', $inspectionDoc ? \App\Helpers\DateHelper::formatForInput($inspectionDoc->expiry_date) : '') }}"
                                        >
                                        @error('documents.0.expiry_date ')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tệp đính kèm</label>
                                    <input type="file" name="documents[0][document_file]" id="documentFile0Input" class="form-control mt-1 border p-2 rounded">
                                    @if(session()->has('_documentFile0_temp'))
                                        <img id="documentFile0Preview" src="{{ session('_documentFile0_temp') }}" class="w-24 h-24 rounded-full mt-4" alt="Document Preview">
                                        <input type="hidden" name="_documentFile0_temp" value="{{ session('_documentFile0_temp') ?? null }}">
                                    @else
                                        <img id="documentFile0Preview" src="{{ (isset($inspectionDoc) && $inspectionDoc->document_file) ? asset('storage/' . $inspectionDoc->document_file) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Document Preview">
                                    @endif
                                    @if (isset($inspectionDoc) && $inspectionDoc->document_file)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $inspectionDoc->document_file) }}" target="_blank">
                                                📎 Xem tệp đã tải lên (đã được lưu)
                                            </a>
                                        </div>
                                    @endif
                                    @error('documents[0][document_file]')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <hr>
                                <h6>Thông tin bảo hiểm</h6>
                                <div class="row mb-3">
                                <input type="text" class="form-control" name="documents[1][document_type]" value="{{ \App\Models\VehicleDocument::TYPE_INSURANCE }}" hidden>
                                <input type="text" class="form-control" name="documents[1][document_id]" value="{{ $insuranceDoc->document_id ?? null }}" hidden>
                                    <div class="col-md-6">
                                        <label class="form-label">Số hợp đồng bảo hiểm </label>
                                        <input type="text" class="form-control" name="documents[1][document_number]" value="{{ old('documents[1][document_number]', $insuranceDoc->document_number ?? null) }}">
                                        @error('documents[1][document_number]')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày hết hạn </label>
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            name="documents[1][expiry_date]" 
                                            placeholder="{{ \App\Helpers\DateHelper::getDateFormatPlaceholder() }}"
                                            value="{{ old('documents[1][expiry_date]', $insuranceDoc ? \App\Helpers\DateHelper::formatForInput($insuranceDoc->expiry_date) : '') }}"
                                        >
                                        @error('documents.1.expiry_date ')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tệp đính kèm </label>
                                    <input 
                                        type="file" 
                                        class="form-control" 
                                        id="documentFile1Input" 
                                        name="documents[1][document_file]"
                                    >
                                    @if(session()->has('_documentFile1_temp'))
                                        <img id="documentFile1Preview" src="{{ session('_documentFile1_temp') }}" class="w-24 h-24 rounded-full mt-4" alt="Document Preview">
                                        <input type="hidden" name="_documentFile1_temp" value="{{ session('_documentFile1_temp') ?? null }}">
                                    @else
                                        <img id="documentFile1Preview" src="{{ (isset($insuranceDoc) && $insuranceDoc->document_file) ? asset('storage/' . $insuranceDoc->document_file) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Document Preview">
                                    @endif
                                    @if (isset($insuranceDoc) && $insuranceDoc->document_file)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $insuranceDoc->document_file) }}" target="_blank">
                                                📎 Xem tệp đã tải lên (đã được lưu)
                                            </a>
                                        </div>
                                    @endif

                                    @error('documents[1][document_file]')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-start">
                                        <button type="submit" class="btn btn-secondary">Lưu</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('documentFile0Input').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('documentFile0Preview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('documentFile1Input').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('documentFile1Preview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
