<div class="modal fade" id="tripRouteModal" tabindex="-1" role="dialog" aria-labelledby="tripRouteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.trip-routes.store') }}" id="trip-route-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tripRouteModalLabel">Thêm lộ trình mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="origin_name">Điểm đi <span class="text-danger">*</span></label>
                        <input type="text" name="origin_name" id="origin_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="destination_name">Điểm đến <span class="text-danger">*</span></label>
                        <input type="text" name="destination_name" id="destination_name" class="form-control"
                            required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tons">Số tấn</label>
                        <input type="number" name="tons" id="tons" class="form-control" step="0.01"
                            min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label for="price">Giá tiền</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01"
                            min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="cancel-trip-route">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // prepare modal for create
            $('#add-trip-route').on('click', function() {
                var $form = $('#trip-route-form');
                $form[0].reset();
                $form.attr('action', '{{ route('admin.trip-routes.store') }}');
                $form.find('input[name=_method]').remove();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();
                $('#tripRouteModalLabel').text('Thêm lộ trình mới');
                $('#tripRouteModal').modal('show');
            });

            $('#cancel-trip-route').on('click', function() {
                $('#tripRouteModal').modal('hide');
            });

            // open modal for edit
            $('.edit-route-btn').on('click', function() {
                var id = $(this).data('id');
                var $form = $('#trip-route-form');
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                // fetch record from server
                $.ajax({
                    url: '/admin/trip-routes/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response.data || response;
                        $form.find('input[name="origin_name"]').val(data.origin_name || data
                            .origin || '');
                        $form.find('input[name="destination_name"]').val(data
                            .destination_name || data.destination || '');
                        $form.find('input[name="tons"]').val(data.tons || 0);
                        $form.find('input[name="price"]').val(data.price || 0);

                        var updateUrl = '/admin/trip-routes/' + id;
                        $form.attr('action', updateUrl);
                        if ($form.find('input[name=_method]').length === 0) {
                            $form.append('<input type="hidden" name="_method" value="PUT">');
                        } else {
                            $form.find('input[name=_method]').val('PUT');
                        }

                        $('#tripRouteModalLabel').text('Chỉnh sửa lộ trình');
                        $('#tripRouteModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Lỗi', 'Không thể tải dữ liệu để chỉnh sửa.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
