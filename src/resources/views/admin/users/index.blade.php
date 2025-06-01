@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">{{ greeting_message() }}</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <a href="{{ route('admin.users.create') }}" type="button" class="btn btn-soft-success"><i class="ri-add-circle-line align-middle me-1"></i> Add User</a>
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
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="form-control border p-2 rounded">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <select name="role" class="form-control border p-2 rounded">
                                    <option value="">All Roles</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <select name="status" class="form-control border p-2 rounded">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Search</button>
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
                                                <th class="py-2 px-4">Full name</th>
                                                <th class="py-2 px-4">Email</th>
                                                <th class="py-2 px-4">Birthday</th>
                                                <th class="py-2 px-4">Avatar</th>
                                                <th class="py-2 px-4">Role</th>
                                                <th class="py-2 px-4">Status</th>
                                                <th class="py-2 px-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td class="py-2 px-4">{{ $user->name }}</td>
                                                    <td class="py-2 px-4">{{ $user->email }}</td>
                                                    <td class="py-2 px-4">{{ $user->birthday }}</td>
                                                    <td class="py-2 px-4">
                                                        <img src="{{ !$user->avatar ? asset('no-image.jpeg') : asset('storage/' . $user->avatar) }}" class="w-10 h-10 rounded-full img-avatar-table" alt="Avatar">
                                                    </td>
                                                    <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                                                    <td class="py-2 px-4">
                                                        @if ($user->status) <span class="badge bg-success-subtle text-success">Active</span>
                                                        @else
                                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-4 flex gap-2">
                                                        <div class="d-flex gap-2">
                                                            <a cl href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-success edit-item-btn">Edit</a>

                                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-user-form">
                                                                {{-- onsubmit="return confirm('Are you sure?')" --}}
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-danger remove-item-btn delete-user-btn">Delete</button>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-user-btn').click(function (e) {
            e.preventDefault();
    
            var form = $(this).closest('.delete-user-form');
    
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