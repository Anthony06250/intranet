/**
 * Init percent field
 */
$(document).ready(function () {
    'use strict';

    $('.field-percent input').on('change', function () {
        let value = $(this).val().toNumber();

        console.log(value);
        $(this).val(value ? value.toCurrency() : null);
    });
});