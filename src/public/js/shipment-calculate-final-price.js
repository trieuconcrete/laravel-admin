$(document).ready(function () {

    function parseNumber(value) {
        if (value === undefined || value === null) return 0;
        value = value.toString().replace(/[, \s]/g, '').replace(/[^\d.-]/g, '');
        return parseFloat(value) || 0;
    }

    function formatNumber(value) {
        if (value === undefined || value === null) return '';
        return Number(value).toLocaleString('en-US');
    }

    // Tính amount cho 1 hàng
    function calculateGoodsAmountForRow($row) {
        let $qty  = $row.find("input[name*='[quantity]']");
        let $wt   = $row.find("input[name*='[weight]']");
        let $unit = $row.find("input[name*='[unit]']");
        let $amt  = $row.find("input[name*='[amount]']");

        if ($amt.length === 0) return 0;

        let amount = parseNumber($qty.val()) * parseNumber($wt.val()) * parseNumber($unit.val());
        $amt.val(formatNumber(amount));

        let $amtRaw = $row.find("input[type='hidden'][name*='[amount]']");
        if ($amtRaw.length) $amtRaw.val(amount);

        return amount;
    }

    // Tổng tiền hàng hóa
    function calculateGoodsTotal() {
        let sum = 0;
        $("input[name^='goods'][name$='[amount]']").each(function () {
            sum += parseNumber($(this).val());
        });
        return sum;
    }

    // Tổng deductions
    function calculateDeductionsTotal() {
        let total = 0;
        $("input[name^='deductions']").each(function () {
            total += parseNumber($(this).val());
        });
        return total;
    }

    // Tính tổng chuyến
    function calculateTripTotal() {
        let shipmentType = $("input[name='shipment_type']:checked").val();
        let deductions = calculateDeductionsTotal();
        let unitPrice = parseNumber($("#total-amount").val());
        let goodsTotal = calculateGoodsTotal();

        let total = (shipmentType === '3')
            ? deductions + goodsTotal
            : deductions + unitPrice;

        $("#trip-total").val(formatNumber(total));
        $("#trip-total-edit").val(Math.round(total));
    }

    // Sự kiện thay đổi trong row hàng hóa
    $(document).on(
        "input keyup change",
        "input[name^='goods'][name$='[quantity]'], input[name^='goods'][name$='[weight]'], input[name^='goods'][name$='[unit]']",
        function () {
            let $row = $(this).closest('tr');
            calculateGoodsAmountForRow($row);
            calculateTripTotal();
        }
    );

    // Nếu user sửa trực tiếp amount
    $(document).on(
        "input keyup change",
        "input[name^='goods'][name$='[amount]']",
        calculateTripTotal
    );

    // Thay đổi deductions
    $(document).on(
        "input keyup change",
        "input[name^='deductions']",
        calculateTripTotal
    );

    // Thay đổi giá chuyến
    $(document).on(
        "input keyup change",
        "#total-amount",
        calculateTripTotal
    );

    // Thay đổi loại chuyến
    $(document).on(
        "change",
        "input[name='shipment_type']",
        calculateTripTotal
    );

    // Khi xóa hàng
    window.removeGoodRow = function (btnOrIndex) {
        if (typeof btnOrIndex === 'number') {
            $("input[name='goods_rows[]'][value='" + btnOrIndex + "']")
                .closest('tr')
                .remove();
        } else {
            $(btnOrIndex).closest('tr').remove();
        }
        calculateTripTotal();
    };

    $("input[name^='goods'][name$='[quantity]']").each(function () {
        calculateGoodsAmountForRow($(this).closest('tr'));
    });

    calculateTripTotal();

});
