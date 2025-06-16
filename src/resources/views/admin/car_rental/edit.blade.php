@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Chi tiết thuê xe</h4>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.car-rental.update', $carRental->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                        <select class="form-select" name="customer_id">
                                            <option value="">Chọn khách hàng</option>
                                            @foreach ($customers as $key => $customer)
                                            <option value="{{ $key }}" {{ $key == $carRental->customer_id ? 'selected' : '' }}>
                                                {{ $customer }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger error" data-field="customer_id"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <select class="form-select" name="status">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach ($carRentalstatuses as $val => $label)
                                            <option value="{{ $val }}" {{ $val == $carRental->status ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger error" data-field="status"></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Mô tả dịch vụ</label>
                                    <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="description" value="{{ old('description', $carRental->description) }}">{{ $carRental->description }}</textarea>
                                    @error('description')
                                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="notes" value="{{ old('notes', $carRental->notes) }}">{{ $carRental->notes }}</textarea>
                                    @error('notes')
                                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700">File thuê xe</label>
                                    {{-- <input type="file" name="file" id="avatarInput" class="form-control mt-1 border p-2 rounded">
                                    <img id="avatarPreview" src="{{ isset($carRental) && $carRental->file ? asset('storage/' . $carRental->file) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="File thuê xe"> --}}
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Danh sách xe thuê</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addGoodBtn">
                                            <i class="fas fa-plus me-1"></i>Thêm phương tiện
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="vehiclesTable">
                                            <thead>
                                                <tr>
                                                    <th width="200">Phương tiện <span class="text-danger">*</span>
                                                    </th>
                                                    <th width="250">Tên hàng</th>
                                                    <th width="120">Đơn vị</th>
                                                    <th width="50">Số lượng</th>
                                                    <th>Đơn giá</th>
                                                    <th>Thành tiền</th>
                                                    <th>Ngày bắt đầu</th>
                                                    <th>Ngày kết thúc</th>
                                                    <th>Ghi chú</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($carRentalVehicles) > 0)
                                                @foreach ($carRentalVehicles as $i => $vehicle)
                                                <tr>
                                                    <td>
                                                        <select class="form-select" name="vehicles[{{ $i }}][vehicle_id]" required>
                                                            <option value="">Chọn phương tiện</option>
                                                            @foreach ($vehicleTypes as $id => $name)
                                                            <option value="{{ $id }}" {{ $id == $vehicle->vehicle_id ? "selected" : "" }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('vehicle_ids')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][product_name]" class="form-control" value="{{ old('vehicles.' . $i . '.product_name', $vehicle['product_name'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.product_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="vehicles[{{ $i }}][[unit]]" required>
                                                            <option value="tháng" {{ "tháng" == $vehicle->unit }}>Tháng</option>
                                                            <option value="ngày" {{ "ngày" == $vehicle->unit }}>Ngày</option>
                                                        </select>
                                                        @error('vehicles.' . $i . '.quantity')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][amount]" class="form-control" min="1" value="{{ old('vehicles.' . $i . '.amount', $vehicle['amount'] ?? '1') }}">
                                                        @error('vehicles.' . $i . '.amount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][price]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.unit', $vehicle['price'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][money]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.money', $vehicle['money'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.money')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][start_date]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.start_date', $vehicle['start_date'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.start_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][end_date]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.end_date', $vehicle['end_date'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.end_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][notes]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.notes', $vehicle['notes'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.notes')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td>
                                                        <select class="form-select" name="vehicle_ids" required>
                                                            <option value="">Chọn phương tiện</option>
                                                        </select>
                                                        <div class="text-danger" id="error-vehicles-0-name">
                                                            @error('vehicles.0.name')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[0][notes]" class="form-control" value="{{ old('vehicles.0.notes') }}">
                                                        <div class="text-danger" id="error-vehicles-0-notes">
                                                            @error('vehicles.0.notes')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="vehicle_ids" required>
                                                            <option value="1">Tháng</option>
                                                            <option value="1">Ngày</option>
                                                        </select>
                                                        <div class="text-danger" id="error-vehicles-0-quantity">
                                                            @error('vehicles.0.quantity')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][weight]" class="form-control" min="1" value="{{ old('vehicles.0.weight') ?? 1 }}">
                                                        <div class="text-danger" id="error-vehicles-0-weight">
                                                            @error('vehicles.0.weight')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeGoodRow(this, 0)"><i class="ri-delete-bin-fill"></i></button>
                                                        <input type="hidden" name="vehicles_rows[]" value="0">
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Save</button>
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
    document.getElementById('avatarInput').addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }

            reader.readAsDataURL(file);
        }
    });

</script>
@endpush
