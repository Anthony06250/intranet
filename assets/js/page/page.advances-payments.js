/**
 * Init advance payments form
 */
$(document).ready(function () {
    'use strict';

    let advance_payments = new AdvancePayments();

    document.addEventListener('ea.collection.item-added', function () {
        advance_payments.initCollection();
    });
});

/**
 * Advance payments class
 */
class AdvancePayments {
    /**
     * Construct invoice class
     */
    constructor () {
        this.initCollection();
    }

    /**
     * Init invoice collection
     */
    initCollection() {
        $('.field-collection-item input[id$=\'_price\']').on('change', function () {
            let value = $(this).val().toNumber();

            $(this).val(value ? value.toCurrency() : null);
        });
    }
}
