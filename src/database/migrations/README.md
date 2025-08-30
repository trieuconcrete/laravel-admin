# Database Migrations

## Không cần migration

Tính năng trợ cấp ăn trưa được tính toán tự động với giá trị mặc định.

## Mô tả tính năng

- **Trợ cấp ăn trưa**: 35,000 VND/ngày
- **Số ngày làm việc**: 22 ngày/tháng
- **Tổng trợ cấp**: 770,000 VND/tháng
- **Áp dụng**: Nhân viên ăn lương cơ bản (trừ tài xế)

## Logic hiển thị

Tính năng này chỉ hiển thị khi:
1. User có `salary_type = 1` (BASIC_SALARY) 
2. User có `role != 'driver'`

## Tính năng mới: Xuất bảng lương văn phòng

### Điều kiện sử dụng
- Chỉ nhân viên văn phòng (không phải tài xế) mới có thể xuất bảng lương văn phòng
- Nhân viên phải có `salary_type = 1` (BASIC_SALARY)

### Cách sử dụng
1. Vào trang thông tin cá nhân của nhân viên
2. Chọn tab "Bảng lương"
3. Chọn tháng cần xuất
4. Nhấn nút "Xuất Bảng Lương Văn Phòng" (màu xanh dương)

### Định dạng bảng lương
Bảng lương văn phòng bao gồm:
- **Lương cơ bản**: Lương cơ bản hàng tháng
- **Phụ cấp cơm ngày**: Trợ cấp ăn trưa (22 ngày × số tiền/ngày)
- **Chi phí khác**: Thưởng, phạt, các khoản khác
- **Trừ BHXH**: Khấu trừ bảo hiểm xã hội
- **Tiền ứng**: Các khoản ứng lương
- **Tổng lương còn lại**: Lương thực nhận

### File export
- **Tên file**: `bang_luong_van_phong_[Tên nhân viên]_[Tháng]_[Timestamp].xlsx`
- **Định dạng**: Excel với styling đẹp, giống mẫu trong hình

## Rollback

Không cần rollback vì không có migration nào được thêm vào database.

## Lưu ý

- Logic xuất bảng lương hiện tại vẫn được giữ nguyên
- File export mới hoàn toàn độc lập, không ảnh hưởng đến chức năng cũ
- Chỉ nhân viên văn phòng mới thấy nút "Xuất Bảng Lương Văn Phòng"
