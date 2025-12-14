@extends('admin.layout')
@section('title', 'Quản lý lộ trình chuyến xe')

@push('styles')
    <style>
        #add-trip-route {
            width: max-content
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="add-trip-route">
                    Thêm lộ trình mới
                </button>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="row">
                <div class="col">
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert">×</button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row g-3 justify-content-between">
                                <div class="col-md-6">
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tìm kiếm theo điểm đi, điểm đến..."
                                        value="{{ $filters['keyword'] ?? '' }}">
                                </div>

                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Điểm Đi</th>
                                        <th>Điểm Đến</th>
                                        <th>Số tấn</th>
                                        <th>Giá tiền</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tripRoutes as $route)
                                        <tr>
                                            <td>{{ $route->origin_name ?? $route->origin }}</td>
                                            <td>{{ $route->destination_name ?? $route->destination }}</td>
                                            <td>{{ $route->tons ?? '-' }}</td>
                                            <td>{{ isset($route->price) ? number_format($route->price, 2, ',', '.') : '-' }}
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-sm btn-outline-primary edit-route-btn"
                                                        data-id="{{ $route->id }}">
                                                        Chỉnh sửa
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-route-btn"
                                                        data-id="{{ $route->id }}">
                                                        Xóa
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Không có lộ trình nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <div class="mt-4">
                                    {{-- {{ $tripRoutes->appends(request()->query())->links() }} --}}
                                    {!! $tripRoutes->withQueryString()->links('vendor.pagination.bootstrap-5') !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.trip-routes.modal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ajax submit for create/update
            $('#trip-route-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                var url = $form.attr('action');
                var data = $form.serialize();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#tripRouteModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Thao tác thành công',
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(key, msgs) {
                                var $input = $form.find('[name="' + key + '"]');
                                if ($input.length) {
                                    $input.addClass('is-invalid');
                                    var msg = Array.isArray(msgs) ? msgs.join('<br>') :
                                        msgs;
                                    if ($input.next('.invalid-feedback').length === 0) {
                                        $input.after('<div class="invalid-feedback">' +
                                            msg + '</div>');
                                    } else {
                                        $input.next('.invalid-feedback').html(msg);
                                    }
                                }
                            });
                        } else {
                            Swal.fire('Lỗi', 'Đã xảy ra lỗi khi lưu lộ trình.', 'error');
                        }
                    }
                });
            });

            // handle delete
            $('.delete-route-btn').on('click', function() {
                var routeId = $(this).data('id');
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Lộ trình này sẽ bị xóa vĩnh viễn!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Có, xóa nó!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/trip-routes/' + routeId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Lỗi!',
                                    'Đã xảy ra lỗi khi xóa lộ trình.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
