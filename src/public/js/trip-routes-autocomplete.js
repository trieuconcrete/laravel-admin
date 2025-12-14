(function ($) {
    $(function () {
        const $origin = $('#origin');
        const $originList = $('#origin-list');
        const $destination = $('#destination');
        const $destinationList = $('#destination-list');
        const $tons = $('#tons');
        const $destination2 = $('#destination2');
        const $destinationList2 = $('#destination-list-2');
        const $tons2 = $('#tons2');
        const $tripPrice2 = $('#trip_price2');
        const $destination3 = $('#destination3');
        const $destinationList3 = $('#destination-list-3');
        const $tons3 = $('#tons3');
        const $tripPrice3 = $('#trip_price3');
        const $priceInput = $('#total-amount');
        const $tripPrice = $('#trip_price');

        let originTimer = null;

        function toTitleCase(str) {
            if (!str) return '';
            function capitalizeToken(token) {
                return token
                    .split(/(-)/)
                    .map(function (part) {
                        if (part === '-') return part;
                        return part.charAt(0).toLocaleUpperCase('vi-VN') + part.slice(1).toLocaleLowerCase('vi-VN');
                    })
                    .join('');
            }

            return String(str)
                .split(/(\s+)/)
                .map(function (part) {
                    if (/^\s+$/.test(part)) return part;
                    return capitalizeToken(part);
                })
                .join('');
        }

        $origin.on('input', function () {
            const q = $(this).val();
            clearTimeout(originTimer);
            if (!q || q.length < 1) return;

            originTimer = setTimeout(function () {
                $.get('/api/trip-routes/suggest', { field: 'origin', q: q }).done(function (list) {
                    $originList.empty();
                    (list || []).forEach(function (it) {
                        $originList.append($('<option>').val(it.name));
                    });
                });
            }, 250);
        });

        $origin.on('blur', function () {
            var v = $(this).val();
            if (v && v.length) $(this).val(toTitleCase(v));
        });

        $origin.on('change', function () {
            const origin = $(this).val();
            if (!origin) return;

            $.get('/api/trip-routes/destinations-by-origin', { origin: origin }).done(function (data) {
                $destinationList.empty();
                if ($destinationList2) $destinationList2.empty();
                if ($destinationList3) $destinationList3.empty();

                (data.destinations || []).forEach(function (d) {
                    const $opt = $('<option>')
                        .val(d.name)
                        .attr('data-price', d.price || '')
                        .attr('data-tons', d.tons || '');

                    $destinationList.append($opt.clone());
                    if ($destinationList2) $destinationList2.append($opt.clone());
                    if ($destinationList3) $destinationList3.append($opt.clone());
                });
            });
        });

        $destination.on('blur', function () {
            var v = $(this).val();
            if (v && v.length) $(this).val(toTitleCase(v));
        });

        if ($destination2.length) {
            $destination2.on('blur', function () {
                var v = $(this).val();
                if (v && v.length) $(this).val(toTitleCase(v));
            });
        }

        if ($destination3.length) {
            $destination3.on('blur', function () {
                var v = $(this).val();
                if (v && v.length) $(this).val(toTitleCase(v));
            });
        }

        function handleDestinationChange($input, $list, $tonsField, $routePriceField) {
            $input.on('change', function () {
                const val = $(this).val().toLowerCase();
                let found = null;

                $list.find('option').each(function () {
                    if ($(this).val().toLowerCase() === val) {
                        found = $(this);
                        return false;
                    }
                });

                if (found) {
                    const tons = found.attr('data-tons') || '';
                    const price = found.attr('data-price') || '';

                    if ($tonsField) $tonsField.val(tons);
                    if ($routePriceField) {
                        $routePriceField.val(price);
                        if (window.formatPriceInput) window.formatPriceInput($routePriceField);
                    }

                    $priceInput.val(price);
                    if (window.formatPriceInput) window.formatPriceInput($priceInput);
                }
            });
        }

        if ($destination.length && $destinationList.length)
            handleDestinationChange($destination, $destinationList, $tons, $tripPrice);

        if ($destination2.length && $destinationList2.length)
            handleDestinationChange($destination2, $destinationList2, $tons2, $tripPrice2);

        if ($destination3.length && $destinationList3.length)
            handleDestinationChange($destination3, $destinationList3, $tons3, $tripPrice3);

        function preloadEditForm() {
            const originVal = $origin.val();
            if (!originVal) return;

            $.get('/api/trip-routes/destinations-by-origin', { origin: originVal }).done(function (data) {
                $destinationList.empty();
                if ($destinationList2) $destinationList2.empty();
                if ($destinationList3) $destinationList3.empty();

                (data.destinations || []).forEach(function (d) {
                    const $opt = $('<option>')
                        .val(d.name)
                        .attr('data-price', d.price || '')
                        .attr('data-tons', d.tons || '');

                    $destinationList.append($opt.clone());
                    if ($destinationList2) $destinationList2.append($opt.clone());
                    if ($destinationList3) $destinationList3.append($opt.clone());
                });

                function applyValue($input, $list, $tonsField, $priceField) {
                    if (!$input.length) return;
                    const v = $input.val();
                    if (!v) return;

                    const lower = v.toLowerCase();
                    let found = null;

                    $list.find('option').each(function () {
                        if ($(this).val().toLowerCase() === lower) {
                            found = $(this);
                            return false;
                        }
                    });

                    if (!found) return;

                    const tons = found.attr('data-tons') || '';
                    const price = found.attr('data-price') || '';

                    if ($tonsField) $tonsField.val(tons);
                    if ($priceField) {
                        $priceField.val(price);
                        if (window.formatPriceInput) window.formatPriceInput($priceField);
                    }
                }

                applyValue($destination, $destinationList, $tons, $tripPrice);
                applyValue($destination2, $destinationList2, $tons2, $tripPrice2);
                applyValue($destination3, $destinationList3, $tons3, $tripPrice3);

                if ($tripPrice.val()) {
                    $priceInput.val($tripPrice.val());
                    if (window.formatPriceInput) window.formatPriceInput($priceInput);
                }
            });
        }

        preloadEditForm();
    });
})(jQuery);
