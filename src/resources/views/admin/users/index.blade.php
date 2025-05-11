@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-group-fill fs-1"></i>Quản lý tài xế và nhân viên</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".add-driver-model"><i class="ri-add-circle-line align-middle me-1"></i> Thêm tài xế</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".add-user-model"><i class="ri-add-circle-line align-middle me-1"></i> Thêm nhân viên</button>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4 mb-4">
                        <div class="row gy-4">
                            <div class="col-xxl-3 col-md-6">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm với Họ tên, SDT, Email..." class="form-control border p-2 rounded">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <select name="position_id" class="form-control border p-2 rounded">
                                    <option value="">Tất cả vị trí</option>
                                    @foreach ($positions as $key => $val )
                                    <option value="{{ $key }}" {{ request('position_id') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <select name="status" class="form-control border p-2 rounded">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang làm việc</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã nghỉ việc</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <button type="submit" class="btn btn-outline-primary w-100">Tìm kiếm</button>
                            </div>
                        </div>
                    </form>
                    <div class="card">
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive table-card">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr class="bg-gray-100">
                                                <th class="py-2 px-4">Mã NV</th>
                                                <th class="py-2 px-4">Họ tên</th>
                                                <th class="py-2 px-4">SDT</th>
                                                <th class="py-2 px-4">Email</th>
                                                <th class="py-2 px-4">Vị trí</th>
                                                <th class="py-2 px-4">Lương cơ bản</th>
                                                <th class="py-2 px-4">Loại bằng lái</th>
                                                <th class="py-2 px-4">Hạn bằng lái</th>
                                                <th class="py-2 px-4">Trạng thái</th>
                                                <th class="py-2 px-4"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td class="py-2 px-4">{{ $user->employee_code }}</td>
                                                    <td class="py-2 px-4">{{ $user->full_name }}</td>
                                                    <td class="py-2 px-4">{{ $user->phone }}</td>
                                                    <td class="py-2 px-4">{{ $user->email }}</td>
                                                    <td class="py-2 px-4">{{ ucfirst($user->position->name) }}</td>
                                                    <td class="py-2 px-4">{{ number_format($user->salary_base) }}</td>
                                                    <td class="py-2 px-4">{{  optional($user->license)->license_type }}</td>
                                                    <td class="py-2 px-4">
                                                        @if (optional($user->license)->status == 'expired')
                                                            <span class="text-danger">{{ optional($user->license)->getStatusLabelAttribute() }}</span>
                                                        @else
                                                            <span class="text-success">{{ optional($user->license)->getStatusLabelAttribute() }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-4">
                                                        @if ($user->status) <span class="badge bg-success-subtle text-success">Đang làm việc</span>
                                                        @else
                                                        <span class="badge bg-danger-subtle text-danger">Đã nghỉ việc</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-4 flex gap-2">
                                                        <div class="btn-group">
                                                            <a cl href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn">xóa</button>
                                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-user-form">
                                                                {{-- onsubmit="return confirm('Are you sure?')" --}}
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div> <!-- end col -->
    </div>

</div>
<!-- container-fluid -->

<!-- Add Driver Model -->
<div class="modal fade add-driver-model" tabindex="-1" role="dialog" aria-labelledby="addDriverModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDriverModel">Thêm tài xế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="javascript:void(0);">
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <label for="fullName" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" placeholder="Nhập họ và tên">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="idNumber" class="form-label">CCCD/CMND</label>
                            <input type="text" class="form-control" placeholder="Nhập CCCD/CMND">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" value="" placeholder="Nhập email">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" value="" placeholder="Nhập ngày sinh">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="date" class="form-label">Ngày vào làm</label>
                            <input type="date" class="form-control" value="" placeholder="Nhập ngày vào làm">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="licenseType" class="form-label">Loại bằng lái</label>
                            <select name="license_type" class="form-control">
                                <option value="">Chọn bằng lái</option>
                                @foreach ($licenses as $key => $val )
                                    <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="date" class="form-label">Hạn bằng lái</label>
                            <input type="date" class="form-control" value="" placeholder="Nhập hạn bằng lái">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="salaryBase" class="form-label">Lương cơ bản</label>
                            <input type="text" class="form-control" name="salary_base" value="" placeholder="Nhập Lương cơ bản">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" placeholder="Nhập địa chỉ">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="address" class="form-label">Ghi chú</label>
                            <textarea row=3 class="form-control" placeholder="Nhập ghi chú"></textarea>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-secondary">Lưu tài xế</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Add User Model -->
<div class="modal fade add-user-model" tabindex="-1" role="dialog" aria-labelledby="addUserModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModel">Thêm nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="javascript:void(0);">
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <label for="fullName" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" placeholder="Nhập họ và tên">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="idNumber" class="form-label">CCCD/CMND</label>
                            <input type="text" class="form-control" placeholder="Nhập CCCD/CMND">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" value="" placeholder="Nhập email">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" value="" placeholder="Nhập ngày sinh">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="date" class="form-label">Ngày vào làm</label>
                            <input type="date" class="form-control" value="" placeholder="Nhập ngày vào làm">
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="licenseType" class="form-label">Vị trí</label>
                            <select name="license_type" class="form-control">
                                <option value="">Chọn vị trí</option>
                                @foreach ($positions as $key => $val )
                                    @if ($val !== 'Tài xế')
                                    <option value="{{ $key }}" {{ request('position_id') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6">
                            <label for="salaryBase" class="form-label">Lương cơ bản</label>
                            <input type="text" class="form-control" name="salary_base" value="" placeholder="Nhập Lương cơ bản">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" placeholder="Nhập địa chỉ">
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <label for="address" class="form-label">Ghi chú</label>
                            <textarea row=3 class="form-control" placeholder="Nhập ghi chú"></textarea>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-secondary">Lưu nhân viên</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-user-btn').click(function (e) {
            e.preventDefault();
    
            var form = $('.delete-user-form');
    
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