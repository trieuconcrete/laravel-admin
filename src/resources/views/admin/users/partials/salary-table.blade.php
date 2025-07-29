<tr class="border-bottom">
    <td class="fw-medium">Lương cơ bản</td>
    <td class="text-end" data-salary="base">{{ number_format($salaryBase) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Trợ cấp</td>
    <td class="text-end" data-salary="allowance">{{ number_format($totalAllowance) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Tiền thưởng</td>
    <td class="text-end" data-salary="bonus">{{ number_format($totalBonus) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Tiền phạt</td>
    <td class="text-end text-danger" data-salary="penalty">- {{ number_format($totalPenalty) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Ứng lương <small class="text-muted">(Đã duyệt/Đã chi)</small></td>
    <td class="text-end text-danger" data-salary="other-deduction">- {{ number_format($totalOtherDeduction) }} đ</td>
</tr>
<tr class="border-bottom bg-soft-light">
    <td class="fw-medium">Tổng trước khấu trừ</td>
    <td class="text-end fw-semibold" data-salary="total-before-deduction">{{ number_format($salaryBase + $totalAllowance) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Trừ BHXH (10%)</td>
    <td class="text-end text-danger" data-salary="insurance">- {{ number_format($insuranceDeduction) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Trừ tiền phạt</td>
    <td class="text-end text-danger" data-salary="penalty">- {{ number_format($totalPenalty) }} đ</td>
</tr>
<tr class="border-bottom">
    <td class="fw-medium">Trừ ứng lương <small class="text-muted">(Đã duyệt/Đã chi)</small></td>
    <td class="text-end text-danger" data-salary="other-deduction">- {{ number_format($totalOtherDeduction) }} đ</td>
</tr>
<tr>
    <td class="fw-bold fs-5">Tổng lương thực nhận</td>
    <td class="text-end fw-bold fs-5 text-success" data-salary="total">{{ number_format($totalSalary) }} đ</td>
</tr> 