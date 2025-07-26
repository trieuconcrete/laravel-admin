@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-currency-fill fs-1"></i> Quản lý lương</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".sync-salary-model">Đồng bộ lương</button>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>

            <!-- Charts & Statistics -->
            <div class="row mb-4">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Thống kê theo bộ phận</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Bộ phận</th>
                                            <th>Số người</th>
                                            <th>Tổng lương</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($departmentStats as $dept)
                                        @if($dept['code'] != \App\Models\Position::POSITION_GD)
                                        <tr>
                                            <td>{{ $dept['name'] }}</td>
                                            <td>{{ $dept['count'] }}</td>
                                            <td>{{ number_format($dept['total_salary'], 0, ',', '.') }} ₫</td>
                                        </tr>
                                        @endif
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Không có dữ liệu bộ phận</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Biểu đồ chi phí lương theo tháng</h6>
                        </div>
                        <div class="card-body">
                            <div id="salary_monthly_chart" data-colors='["--vz-primary"]' class="apex-charts" dir="ltr"></div>
                        </div>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Chart data
                            var chartData = @json($chartData);
                            
                            // Prepare series and categories
                            var months = [];
                            var salaryData = [];
                            
                            chartData.forEach(function(item) {
                                months.push(item.month);
                                salaryData.push(Math.round(item.total / 1000000)); // Convert to millions for better display
                            });
                            
                            // Chart options
                            var options = {
                                chart: {
                                    height: 350,
                                    type: 'bar',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                plotOptions: {
                                    bar: {
                                        dataLabels: {
                                            position: 'top'
                                        },
                                        columnWidth: '40%',
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(val) {
                                        return val + ' triệu';
                                    },
                                    offsetY: -20,
                                    style: {
                                        fontSize: '12px',
                                        colors: ["#304758"]
                                    }
                                },
                                series: [{
                                    name: 'Tổng lương',
                                    data: salaryData
                                }],
                                xaxis: {
                                    categories: months,
                                    position: 'bottom',
                                    axisBorder: {
                                        show: false
                                    },
                                    axisTicks: {
                                        show: false
                                    },
                                    crosshairs: {
                                        fill: {
                                            type: 'gradient',
                                            gradient: {
                                                colorFrom: '#D8E3F0',
                                                colorTo: '#BED1E6',
                                                stops: [0, 100],
                                                opacityFrom: 0.4,
                                                opacityTo: 0.5,
                                            }
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                    }
                                },
                                yaxis: {
                                    axisBorder: {
                                        show: false
                                    },
                                    axisTicks: {
                                        show: false,
                                    },
                                    labels: {
                                        show: true,
                                        formatter: function(val) {
                                            return val + ' triệu';
                                        }
                                    }
                                },
                                title: {
                                    text: 'Chi phí lương theo tháng (triệu đồng)',
                                    floating: false,
                                    offsetY: 0,
                                    align: 'center',
                                    style: {
                                        color: '#444',
                                        fontWeight: '500',
                                    }
                                },
                                colors: ['#4e73df']
                            };
                            
                            // Initialize chart
                            var chart = new ApexCharts(document.querySelector("#salary_monthly_chart"), options);
                            chart.render();
                        });
                        </script>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
            </div>

            <!-- Pending Salary Advance Requests Section -->
            <div class="card mb-4">
                <div class="card-header bg-warning bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-warning">
                            <i class="ri-time-line me-2"></i>
                            Yêu cầu ứng lương chờ duyệt
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-warning fs-6">{{ $pendingAdvanceRequests->total() }} yêu cầu</span>
                            @if($pendingAdvanceRequests->hasPages())
                            <small class="text-muted">
                                Trang {{ $pendingAdvanceRequests->currentPage() }} / {{ $pendingAdvanceRequests->lastPage() }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã yêu cầu</th>
                                    <th>Tên nhân viên</th>
                                    <th>Số tiền ứng</th>
                                    <th>Ngày ứng</th>
                                    <th>Thời gian tạo</th>
                                    <th>Lý do</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($pendingAdvanceRequests->count() > 0)
                                    @foreach($pendingAdvanceRequests as $request)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $request->request_code }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $request->user->full_name }}</h6>
                                                    <small class="text-muted">{{ $request->user->employee_code ?? 'NV' . str_pad($request->user->id, 3, '0', STR_PAD_LEFT) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ number_format($request->amount, 0, ',', '.') }} ₫</span>
                                        </td>
                                        <td>
                                            <span title="{{ $request->request_date ? $request->request_date->format('d/m/Y H:i:s') : 'N/A' }}">
                                                {{ $request->request_date ? $request->request_date->format('d/m/Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span title="{{ $request->created_at ? $request->created_at->format('d/m/Y H:i:s') : 'N/A' }}">
                                                {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $request->reason }}">
                                                {{ $request->reason ?: 'Không có lý do' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="processAdvanceRequest({{ $request->id }}, 'approve')"
                                                        title="Duyệt yêu cầu"
                                                        data-request-id="{{ $request->id }}">
                                                    <i class="ri-check-line"></i> Duyệt
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="processAdvanceRequest({{ $request->id }}, 'reject')"
                                                        title="Từ chối yêu cầu"
                                                        data-request-id="{{ $request->id }}">
                                                    <i class="ri-close-line"></i> Từ chối
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">Không có yêu cầu ứng lương chờ duyệt</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($pendingAdvanceRequests->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-muted">
                                Hiển thị {{ $pendingAdvanceRequests->firstItem() ?? 0 }} - {{ $pendingAdvanceRequests->lastItem() ?? 0 }} 
                                trong tổng số {{ $pendingAdvanceRequests->total() }} yêu cầu
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0">Hiển thị:</label>
                                <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            {{ $pendingAdvanceRequests->appends(request()->query())->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.salary.index') }}" method="GET">
                        <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" id="periodFilter" name="month">
                                @php
                                    $currentMonth = Carbon\Carbon::now();
                                    // Generate last 12 months
                                    for($i = 0; $i < 12; $i++) {
                                        $monthOption = $currentMonth->copy()->subMonths($i)->format('m/Y');
                                        $selected = $selectedMonth == $monthOption ? 'selected' : '';
                                        echo "<option value=\"$monthOption\" $selected>Tháng $monthOption</option>";
                                    }
                                @endphp
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter" name="department">
                                <option value="">Bộ phận: Tất cả</option>
                                @foreach($departmentStats as $dept)
                                <option value="{{ $dept['name'] }}" {{ request('department') == $dept['name'] ? 'selected' : '' }}>{{ $dept['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm nhân viên...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="ri-search-line"></i>  Tìm kiếm
                            </button>
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Salary Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thao tác</th>
                                    <th>Mã NV</th>
                                    <th>Họ và tên</th>
                                    <th>Bộ phận</th>
                                    <th>Lương cơ bản</th>
                                    <th>Phụ cấp</th>
                                    <th>Chi phí chuyến hàng</th>
                                    <th>Thưởng</th>
                                    <th>Phạt</th>
                                    <th>Ứng lương</th>
                                    <th>Tổng trước BHXH</th>
                                    <th>BHXH (10%)</th>
                                    <th>Tổng lương</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                <tr data-period="{{ $selectedMonth }}" data-status="{{ $salary['status'] }}">
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', ['user' => $salary['user_id']]) }}" class="btn btn-sm btn-outline-primary">
                                                Chi tiết
                                            </a>
                                        </div>
                                    </td>
                                    <td>{{ $salary['employee_code'] }}</td>
                                    <td>{{ $salary['name'] }}</td>
                                    <td>{{ $salary['department'] }}</td>
                                    <td>{{ number_format($salary['base_salary']) }} ₫</td>
                                    <td>{{ number_format($salary['allowance']) }} ₫</td>
                                    <td>{{ number_format($salary['total_expenses']) }} ₫</td>
                                    <td>{{ number_format($salary['bonus']) }} ₫</td>
                                    <td>{{ number_format($salary['penalty']) }} ₫</td>
                                    <td>{{ number_format($salary['other_deduction']) }} ₫</td>
                                    <td>{{ number_format($salary['total_salary']) }} ₫</td>
                                    <td>{{ number_format($salary['insurance']) }} ₫</td>
                                    <td>{{ number_format($salary['total']) }} ₫</td>
                                    <td>
                                        @if($salary['status'] == 'paid')
                                            <span class="status-indicator status-paid text-success badge bg-success-subtle">Đã thanh toán</span>
                                        @elseif($salary['status'] == 'pending')
                                            <span class="status-indicator status-pending text-warning badge bg-warning-subtle">Chờ duyệt</span>
                                        @elseif($salary['status'] == 'unpaid')
                                            <span class="status-indicator status-unpaid text-danger badge bg-danger-subtle">Chưa thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Không có dữ liệu lương</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div> <!-- end col -->
    </div>

</div>
<!-- container-fluid -->

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tab functionality
        $('.nav-tabs .nav-link').click(function(e) {
            e.preventDefault();
            const status = $(this).data('status');
            
            // Activate the clicked tab
            $('.nav-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Save active tab to localStorage
            localStorage.setItem('activeSalaryTab', status);
            
            // Show all rows if 'all' is selected
            if (status === 'all') {
                $('tbody tr').show();
            } else {
                // Hide all rows first
                $('tbody tr').hide();
                
                // Show only rows with matching status
                $('tbody tr').each(function() {
                    const rowStatus = $(this).find('.status-indicator').text().trim().toLowerCase();
                    
                    if (status === 'paid' && rowStatus === 'đã thanh toán') {
                        $(this).show();
                    } else if (status === 'pending' && rowStatus === 'chờ duyệt') {
                        $(this).show();
                    } else if (status === 'unpaid' && rowStatus === 'chưa thanh toán') {
                        $(this).show();
                    }
                });
            }
        });
        
        // Client-side search filtering
        $('input[name="search"]').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            
            if (searchText.length > 0) {
                $('tbody tr:visible').each(function() {
                    const rowName = $(this).find('td:eq(2)').text().toLowerCase();
                    const rowCode = $(this).find('td:eq(1)').text().toLowerCase();
                    
                    if (rowName.includes(searchText) || rowCode.includes(searchText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
        
        // Update tab counts
        function updateTabCounts() {
            const totalRows = $('tbody tr').length;
            const paidRows = $('tbody tr').filter(function() {
                return $(this).find('.status-paid').length > 0;
            }).length;
            const pendingRows = $('tbody tr').filter(function() {
                return $(this).find('.status-pending').length > 0;
            }).length;
            const unpaidRows = $('tbody tr').filter(function() {
                return $(this).find('.status-unpaid').length > 0;
            }).length;
            
            $('#all-count').text(totalRows);
            $('#paid-count').text(paidRows);
            $('#pending-count').text(pendingRows);
            $('#unpaid-count').text(unpaidRows);
        }
        
        // Call on page load
        updateTabCounts();
        
        // Restore active tab from localStorage if exists
        const savedTab = localStorage.getItem('activeSalaryTab');
        if (savedTab) {
            $('.nav-tabs .nav-link[data-status="' + savedTab + '"]').click();
        }
    });

    function getChartColorsArray(t) {
        if (null !== document.getElementById(t)) {
            var e = document.getElementById(t).getAttribute("data-colors");
            if (e) return (e = JSON.parse(e)).map(function(t) {
                var e = t.replace(" ", "");
                return -1 === e.indexOf(",") ? getComputedStyle(document.documentElement).getPropertyValue(e) || e : 2 == (t = t.split(",")).length ? "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(t[0]) + "," + t[1] + ")" : e
            });
            console.warn("data-colors Attribute not found on:", t)
        }
    }

    function getChartColorsArray(t) {
        if (null !== document.getElementById(t)) {
            var e = document.getElementById(t).getAttribute("data-colors");
            if (e) return (e = JSON.parse(e)).map(function(t) {
                var e = t.replace(" ", "");
                return -1 === e.indexOf(",") ? getComputedStyle(document.documentElement).getPropertyValue(e) || e : 2 == (t = t.split(",")).length ? "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(t[0]) + "," + t[1] + ")" : e
            });
            console.warn("data-colors Attribute not found on:", t)
        }
    }
    var options, chart, chartColumnDatatalabelColors = getChartColorsArray("column_chart_datalabel"),
        chartPieGradientColors = (chartColumnDatatalabelColors && (options = {
            chart: {
                height: 275,
                type: "bar",
                toolbar: {
                    show: !1
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        position: "top"
                    }
                }
            },
            dataLabels: {
                enabled: !0,
                formatter: function(t) {
                    return t + "%"
                },
                offsetY: -20,
                style: {
                    fontSize: "12px",
                    colors: ["#adb5bd"]
                }
            },
            series: [{
                name: "Tổng",
                data: [2.5, 3.2, 5, 10.1, 4.2, 3.8, 3, 2.4, 4, 1.2, 3.5, .8]
            }],
            colors: chartColumnDatatalabelColors,
            grid: {
                borderStyle: "dashed"
            },
            xaxis: {
                categories: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
                crosshairs: {
                    fill: {
                        type: "gradient",
                        gradient: {
                            colorFrom: "#D8E3F0",
                            colorTo: "#BED1E6",
                            stops: [0, 100],
                            opacityFrom: .4,
                            opacityTo: .5
                        }
                    }
                }
            },
            fill: {
                gradient: {
                    shade: "light",
                    type: "horizontal",
                    shadeIntensity: .25,
                    gradientToColors: void 0,
                    inverseColors: !0,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [50, 0, 100, 100]
                }
            },
            yaxis: {
                labels: {
                    formatter: function(t) {
                        return t + "%"
                    }
                }
            }
        }, (chart = new ApexCharts(document.querySelector("#column_chart_datalabel"), options)).render()), getChartColorsArray("gradient_chart"));
    chartPieGradientColors && (options = {
        series: [44, 55, 24],
        chart: {
            height: 210,
            type: "donut"
        },
        plotOptions: {
            pie: {
                startAngle: -90,
                endAngle: 270
            }
        },
        labels: ["Desktop", "Mobile", "Laptop"],
        dataLabels: {
            enabled: !1
        },
        fill: {
            type: "gradient"
        },
        legend: {
            position: "bottom"
        },
        colors: chartPieGradientColors
    }, (chart = new ApexCharts(document.querySelector("#gradient_chart"), options)).render());
</script>

<script>
    // Function to change items per page
    function changePerPage(perPage) {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('per_page', perPage);
        // Reset to page 1 when changing per_page
        currentUrl.searchParams.delete('page');
        window.location.href = currentUrl.toString();
    }
    
    // Function to process salary advance request (approve/reject)
    function processAdvanceRequest(requestId, action) {
        const actionText = action === 'approve' ? 'duyệt' : 'từ chối';
        const confirmText = action === 'approve' ? 'Bạn có chắc chắn muốn duyệt yêu cầu này?' : 'Bạn có chắc chắn muốn từ chối yêu cầu này?';
        
        Swal.fire({
            title: 'Xác nhận',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: action === 'approve' ? 'Duyệt' : 'Từ chối',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable buttons for this request
                const buttons = document.querySelectorAll(`[data-request-id="${requestId}"]`);
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
                });
                
                // Show loading
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send request
                const url = `{{ url('admin/salary/process-advance-request') }}/${requestId}`;
                console.log('Sending request to:', url);
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token:', csrfToken);
                
                const requestData = {
                    action: action,
                    notes: ''
                };
                console.log('Request data:', requestData);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            // Reload page to refresh the list with current query parameters
                            const currentUrl = new URL(window.location);
                            window.location.href = currentUrl.toString();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã xảy ra lỗi không xác định',
                            confirmButtonText: 'Đóng'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Re-enable buttons
                    const buttons = document.querySelectorAll(`[data-request-id="${requestId}"]`);
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        if (btn.classList.contains('btn-success')) {
                            btn.innerHTML = '<i class="ri-check-line"></i> Duyệt';
                        } else {
                            btn.innerHTML = '<i class="ri-close-line"></i> Từ chối';
                        }
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi xử lý yêu cầu. Vui lòng thử lại sau.',
                        confirmButtonText: 'Đóng'
                    });
                });
            }
        });
    }
</script>

<!-- Sync Salary Modal -->
<div class="modal fade sync-salary-model" tabindex="-1" role="dialog" aria-labelledby="syncSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncSalaryModalLabel">Đồng bộ dữ liệu lương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="syncSalaryForm" action="{{ route('admin.salary.sync') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="salaryPeriodMonth" class="form-label">Kỳ lương</label>
                        <select class="form-select" id="salaryPeriodMonth" name="month" required>
                            @php
                                $currentMonth = Carbon\Carbon::now();
                                // Generate last 12 months
                                for($i = 0; $i < 12; $i++) {
                                    $monthOption = $currentMonth->copy()->subMonths($i)->format('m/Y');
                                    $selected = $selectedMonth == $monthOption ? 'selected' : '';
                                    echo "<option value=\"$monthOption\" $selected>Tháng $monthOption</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="salaryPeriodName" class="form-label">Tên kỳ lương</label>
                        <input type="text" class="form-control" id="salaryPeriodName" name="period_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="salaryPeriodNotes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="salaryPeriodNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="syncSalaryBtn">Đồng bộ</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Update period name when month changes
        $('#salaryPeriodMonth').on('change', function() {
            const selectedMonth = $(this).val();
            $('#salaryPeriodName').val('Kỳ lương tháng ' + selectedMonth);
        });
        
        // Trigger change on load to set initial value
        $('#salaryPeriodMonth').trigger('change');
        
        // Handle sync button click
        $('#syncSalaryBtn').on('click', function() {
            // Show loading indicator
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            $(this).prop('disabled', true);
            
            // Submit the form
            $('#syncSalaryForm').submit();
        });
    });
</script>
@endpush