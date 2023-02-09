/**
 * Init invoice form
 */
$(document).ready(function () {
    'use strict';

    let invoice = new Invoice();

    document.addEventListener('ea.collection.item-added', function () {
        invoice.initCollection();
    });

    document.addEventListener('ea.collection.item-removed', function () {
        invoice.calcTotalWithTaxes();
    });

    $('#Invoices_taxesRate').on('change', function () {
        invoice.calcTotalWithoutTaxes();
    });
});

/**
 * Invoice class
 */
class Invoice {
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
        let invoice = this;

        $('.field-collection-item input[id$=\'_price\']').on('change', function () {
            let value = $(this).val().toNumber();

            $(this).val(value ? value.toCurrency() : null);
            invoice.calcTotalWithTaxes();
        });
    }

    /**
     * Calculate the total with taxes value
     */
    calcTotalWithTaxes() {
        let total = 0;

        $('.field-collection-item input[id$=\'_price\']').each(function () {
            total += $(this).val().toNumber();
        });

        $('#Invoices_totalWithTaxes').val(total ? total.toCurrency() : null);
        this.calcTotalWithoutTaxes();
    }

    /**
     * Calculate the price without taxes
     */
    calcTotalWithoutTaxes() {
        let priceWithTaxes = $('#Invoices_totalWithTaxes').val().toNumber();
        let taxesRate = $('#Invoices_taxesRate').find(':selected').attr('data-rate') * 100;
        let taxes = priceWithTaxes * taxesRate / (100 + taxesRate);

        $('#Invoices_totalWithoutTaxes').val(priceWithTaxes ? (priceWithTaxes - taxes).toCurrency() : null);
        this.updateTaxesAmount(taxes);
    }

    /**
     * Update taxes amount value
     * @param taxes
     */
    updateTaxesAmount(taxes) {
        $('#Invoices_taxesAmount').val(taxes ? taxes.toCurrency() : null);
    }
}
