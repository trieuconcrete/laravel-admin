<tr>
    <td>
        <select class="form-select" name="vehicles[__index__][vehicle_id]" required>
            <option value="">Chọn phương tiện</option>
            @foreach ($vehicleTypes as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <div class="text-danger" id="error-vehicles-__index__-vehicle_id"></div>
    </td>
    <td>
        <input type="text" name="vehicles[__index__][product_name]" class="form-control">
        <div class="text-danger" id="error-vehicles-__index__-product_name"></div>
    </td>
    <td>
        <select class="form-select" name="vehicles[__index__][unit]" required>
            <option value="tháng">Tháng</option>
            <option value="ngày">Ngày</option>
        </select>
        <div class="text-danger" id="error-vehicles-__index__-unit"></div>
    </td>
    <td>
        <input type="number" name="vehicles[__index__][amount]" class="form-control" min="1" value="1">
        <div class="text-danger" id="error-vehicles-__index__-amount"></div>
    </td>
    <td>
        <input type="number" name="vehicles[__index__][price]" class="form-control unit-input">
        <div class="text-danger" id="error-vehicles-__index__-price"></div>
    </td>
    <td>
        <input type="number" name="vehicles[__index__][money]" class="form-control money-input" readonly>
        <div class="text-danger" id="error-vehicles-__index__-money"></div>
    </td>
    <td>
        <input type="text" class="form-control flatpickr-date" name="vehicles[__index__][start_date]" placeholder="yyyy/mm/dd">
        <div class="text-danger" id="error-vehicles-__index__-start_date"></div>
    </td>
    <td>
        <input type="text" class="form-control flatpickr-date" name="vehicles[__index__][end_date]" placeholder="yyyy/mm/dd">
        <div class="text-danger" id="error-vehicles-__index__-end_date"></div>
    </td>
    <td>
        <input type="text" name="vehicles[__index__][notes]" class="form-control">
        <div class="text-danger" id="error-vehicles-__index__-notes"></div>
    </td>
    <td>
        <button type="button" class="btn btn-outline-danger remove-row">
            <i class="ri-delete-bin-fill"></i>
        </button>
        <input type="hidden" name="vehicles_rows[]" value="__index__">
    </td>
</tr>
