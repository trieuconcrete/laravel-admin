document.addEventListener('DOMContentLoaded', function () {
    // Handle month dropdown change
    const monthSelect = document.getElementById('month');
    if (monthSelect) {
        monthSelect.addEventListener('change', function() {
            const selectedMonth = this.value;
            console.log('Month dropdown changed to:', selectedMonth);
            
            if (selectedMonth && selectedMonth !== '') {
                // Calculate start and end date for the selected month
                const [year, month] = selectedMonth.split('-');
                const startDate = `${year}-${month}-01`;
                // Fix: month is 1-indexed for Date constructor, so we need to use month-1 for current month
                const lastDay = new Date(year, month, 0).getDate();
                const endDate = `${year}-${month}-${lastDay}`;
                
                console.log('Calculated dates:', { startDate, endDate, year, month, lastDay });
                
                // Update date inputs
                document.querySelector('input[name="statement_start_date"]').value = startDate;
                document.querySelector('input[name="statement_end_date"]').value = endDate;
                
                // Get current shipment type
                const shipmentType = document.querySelector('select[name="shipment_type"]').value;
                
                console.log('Shipment type:', shipmentType);
                
                // Perform search automatically (with or without shipment type)
                performSearch(startDate, endDate, shipmentType);
                
                // Load debt summary after search (tổng công nợ từ trước đến nay)
                loadDebtSummary();
            } else {
                // If "Chọn ngày tùy ý" is selected, set current month dates but don't search
                console.log('"Chọn ngày tùy ý" selected, setting current month dates');
                const currentDate = new Date();
                const year = currentDate.getFullYear();
                const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                const startDate = `${year}-${month}-01`;
                const lastDay = new Date(year, month, 0).getDate();
                const endDate = `${year}-${month}-${lastDay}`;
                
                document.querySelector('input[name="statement_start_date"]').value = startDate;
                document.querySelector('input[name="statement_end_date"]').value = endDate;
                
                // Auto-search with all types when no specific type is selected
                performSearch(startDate, endDate, '');
            }
        });
    }

    // Handle invoice export button click
    const invoiceButton = document.getElementById('exportInvoice');
    if (invoiceButton) {
        invoiceButton.addEventListener('click', function () {
            // Get selected dates and shipment type
            const startDate = document.getElementById('statement_start_date').value;
            const endDate = document.getElementById('statement_end_date').value;
            const shipmentType = document.querySelector('select[name="shipment_type"]').value;

            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn ngày bắt đầu và ngày kết thúc để xuất.'
                });
                return;
            }

            if (!shipmentType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn loại chuyến xe để xuất.'
                });
                return;
            }

            // Get shipment type label
            const shipmentTypeLabel = getShipmentTypeLabel(shipmentType);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Xác nhận xuất bảng kê?',
                html: `
                    <div class="text-start">
                        <p><strong>Thời gian:</strong> ${startDate} - ${endDate}</p>
                        <p><strong>Loại chuyến xe:</strong> ${shipmentTypeLabel}</p>
                    </div>
                `,
                input: "select",
                inputOptions: {
                    '1': 'TOPBAND',
                    '2': 'WOOJIN',
                    '3': 'Khác',
                },
                inputPlaceholder: "Chọn mẫu bảng kê",
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value !== "") {
                            resolve();
                        } else {
                            resolve("Vui lòng chọn mẫu bảng kê.");
                        }
                    });
                },
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có, xuất ngay',
                cancelButtonText: 'Hủy bỏ',
                customClass: {
                    confirmButton: 'btn btn-secondary',
                    cancelButton: 'btn btn-light'
                }
            }).then((result) => {
                console.log('Export confirmation result:', result);
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ trong giây lát',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();

                            // Create download link
                            const params = new URLSearchParams({
                                statement_start_date: startDate,
                                statement_end_date: endDate,
                                shipment_type: shipmentType,
                                month: monthSelect ? monthSelect.value : '',
                                template_type: result.value
                            });

                            const downloadUrl = `{{ route('admin.shipment-reports.export', $customer) }}?${params.toString()}`;
                            
                            // Create a temporary link and trigger download
                            const link = document.createElement('a');
                            link.href = downloadUrl;
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            // Show success message after download starts
                            setTimeout(() => {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Xuất bảng kê thành công',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }, 1000);
                        }
                    });
                }
            });
        });
    }

    // Helper function to get shipment type label
    function getShipmentTypeLabel(shipmentType) {
        const typeLabels = {
            '1': 'Khách chạy theo chuyến',
            '2': 'Khách thuê xe tháng',
            '3': 'Xe nâng',
            '4': 'Xe đường dài bắc-nam'
        };
        return typeLabels[shipmentType] || 'Loại chuyến xe';
    }

    // Function to perform search with given parameters
    function performSearch(startDate, endDate, shipmentType = '') {
        console.log('=== performSearch called ===');
        console.log('Parameters:', { startDate, endDate, shipmentType });
        console.log('Start date type:', typeof startDate);
        console.log('End date type:', typeof endDate);
        
        // Build query parameters - if shipmentType is empty, don't include it in query
        const queryParams = new URLSearchParams({
            statement_start_date: startDate,
            statement_end_date: endDate
        });
        
        if (shipmentType && shipmentType !== '') {
            queryParams.append('shipment_type', shipmentType);
        }

        console.log('Query params:', queryParams.toString());

        // Show loading
        const searchButton = document.getElementById('searchShipments');
        if (searchButton) {
            searchButton.disabled = true;
            const originalText = searchButton.innerHTML;
            searchButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tìm kiếm...';

            const apiUrl = `{{ route('admin.shipment-reports.data', $customer) }}?${queryParams.toString()}`;
            console.log('Making fetch request to:', apiUrl);
            console.log('Full URL:', window.location.origin + apiUrl);

            // Load data
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                console.log('=== Response received ===');
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('=== Response data parsed ===');
                console.log('Response data:', data);
                console.log('Data success:', data.success);
                console.log('Data data:', data.data);
                console.log('Data total_count:', data.total_count);
                if (data.success) {
                    console.log('=== API call successful ===');
                    console.log('Calling updateTableWithData with:', data.data);
                    
                    // Update table with data
                    updateTableWithData(data.data);
                    
                    // Load debt summary after updating table (tổng công nợ từ trước đến nay)
                    loadDebtSummary();
                    
                    // Show success message
                    const shipmentType = document.querySelector('select[name="shipment_type"]').value;
                    const typeLabel = shipmentType && shipmentType !== '' ? getShipmentTypeLabel(shipmentType) : 'Tất cả các loại';
                    
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Thành công',
                    //     text: `Tìm thấy ${data.total_count} chuyến xe (${typeLabel}) với tổng tiền ${numberFormat(data.total_amount)} VND`,
                    //     timer: 2000,
                    //     showConfirmButton: false
                    // });
                } else {
                    console.log('=== API returned error ===');
                    console.log('API error data:', data);
                    console.log('API error message:', data.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra khi tìm kiếm.'
                    });
                }
            })
            .catch(error => {
                console.error('=== Error occurred ===');
                console.error('Error type:', error.constructor.name);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi tìm kiếm.'
                });
            })
            .finally(() => {
                console.log('=== Request completed ===');
                console.log('Re-enabling search button');
                searchButton.disabled = false;
                searchButton.innerHTML = originalText;
            });
        }
    }
});