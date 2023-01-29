/**
 * Init money field
 */
$(document).ready(function () {
    'use strict';

    $('.field-money input').on('change', function () {
        let value = $(this).val().toNumber();

        $(this).val(value ? value.toCurrency() : null);
    }).trigger('change')
        .parents('form').on('submit', function () {
            $(this).find('.field-money input').each(function () {
                let value = $(this).val();

                $(this).val(value ? value.toNumber() : null);
            });
        });
});

/**
 * Convert currency to number
 * * @returns {number}
 */
String.prototype.toNumber = function () {
    let buffer = this.replace(/,/g, '.').split('.');
    let result;

    if (buffer.length > 1) {
        result = buffer.slice(0, -1).join().replace(/[^0-9-]/g, '')
            + '.' + buffer.slice(-1)[0];
    } else {
        result = buffer.join().replace(/[^0-9-]/g, '');
    }

    return Number(result);
};

/**
 * Convert number to currency
 * @returns {string}
 */
Number.prototype.toCurrency = function () {
    return new Intl.NumberFormat($('html').attr('lang'), {
        style:'currency',
        currency: 'EUR'
    }).formatToParts(this)
        .filter(x => x.type !== "currency")
        .map(x => x.value)
        .join("")
        .trim();
};
