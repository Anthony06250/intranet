/**
 * Init deposits sales form
 */
$(document).ready(function () {
    'use strict';

    let deposit_sales = new DepositSales();

    document.addEventListener('ea.collection.item-added', function () {
        deposit_sales.initCollection();
    });

    document.addEventListener('ea.collection.item-removed', function () {
        deposit_sales.calcReservedPrice();
    });
});

/**
 * Deposit sales class
 */
class DepositSales {
    /**
     * Construct deposit sales class
     */
    constructor () {
        this.initCollection();
    }

    /**
     * Init deposit sales collection
     */
    initCollection() {
        let deposit_sales = this;

        $('.field-collection-item input[id$=\'_price\']').on('change', function () {
            let value = $(this).val().toNumber();

            $(this).val(value ? value.toCurrency() : null);
            deposit_sales.calcReservedPrice();
        });
    }

    /**
     * Calculate the reserved price
     */
    calcReservedPrice() {
        let total = 0;

        $('.field-collection-item input[id$=\'_price\']').each(function () {
            total += $(this).val().toNumber();
        });

        $('#DepositsSales_reserved_price').val(total ? total.toCurrency() : null);
    }
}
