function addVehicleRow() {
    const $tableBody = $('#vehicle-rows');
    const index = $tableBody.find('tr').length;

    const template = $('#vehicle-row-template').html();
    const rowHtml = template.replaceAll('__index__', index);
    const $row = $(rowHtml);

    $tableBody.append($row);

    $row.find('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
    });
}

function removeVehicleRow() {
    $(this).closest('tr').remove();
}
