/**
 * Init integer field
 */
$(document).ready(function () {
    'use strict';

    $('.field-integer input').on('change', function () {
        let value = $(this).val().toNumber();

        $(this).val(value ? value : null);
    });
});