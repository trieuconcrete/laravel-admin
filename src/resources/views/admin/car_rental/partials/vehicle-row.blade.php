@php $index = '__index__'; @endphp
<tr>
    <td>
        <select class="form-select" name="vehicles[{{ $index }}][vehicle_id]">
            <option value="">Chọn phương tiện</option>
            @foreach ($vehicleTypes as $id => $name)
            <option value="{{ $id }}" {{ old("vehicles.$index.vehicle_id") == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
            @endforeach
        </select>
        <div class="text-danger error" data-field="vehicles.{{ $index }}.vehicle_id"></div>
    </td>
    <td>
        <input type="text" name="vehicles[{{ $index }}][product_name]" class="form-control" value="{{ old("vehicles.$index.product_name") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.product_name"></div>
    </td>
    <td>
        <select class="form-select" name="vehicles[{{ $index }}][unit]" required>
            <option value="tháng" {{ old("vehicles.$index.unit") == 'tháng' ? 'selected' : '' }}>Tháng</option>
            <option value="ngày" {{ old("vehicles.$index.unit") == 'ngày' ? 'selected' : '' }}>Ngày</option>
        </select>
        <div class="text-danger error" data-field="vehicles.{{ $index }}.unit"></div>
    </td>
    <td>
        <input type="number" name="vehicles[{{ $index }}][amount]" class="form-control" min="1" value="{{ old("vehicles.$index.amount", 1) }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.amount"></div>
    </td>
    <td>
        <input type="number" name="vehicles[{{ $index }}][price]" class="form-control unit-input" value="{{ old("vehicles.$index.price") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.price"></div>
    </td>
    <td>
        <input type="number" name="vehicles[{{ $index }}][money]" class="form-control money-input" value="{{ old("vehicles.$index.money") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.money"></div>
    </td>
    <td>
        <input type="text" class="form-control flatpickr-date" name="vehicles[{{ $index }}][start_date]" placeholder="yyyy/mm/dd" value="{{ old("vehicles.$index.start_date") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.start_date"></div>
    </td>
    <td>
        <input type="text" class="form-control flatpickr-date" name="vehicles[{{ $index }}][end_date]" placeholder="yyyy/mm/dd" value="{{ old("vehicles.$index.end_date") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.end_date"></div>
    </td>
    <td>
        <input type="text" name="vehicles[{{ $index }}][notes]" class="form-control" value="{{ old("vehicles.$index.notes") }}">
        <div class="text-danger error" data-field="vehicles.{{ $index }}.notes"></div>
    </td>
    <td>
        <button type="button" class="btn btn-outline-danger remove-row">
            <i class="ri-delete-bin-fill"></i>
        </button>
        <input type="hidden" name="vehicles_rows[]" value="{{ $index }}">
    </td>
</tr>
