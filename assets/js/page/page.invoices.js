/**
 * Init invoice form
 */
$(document).ready(function () {
    'use strict';

    let invoice = new Invoice();

    $('#Invoices_productPrice').on('change', function () {
        invoice.updateTotalWithTaxes();
    });

    $('#Invoices_taxesRate').on('change', function () {
        invoice.calcTotalWithoutTaxes();
    });

    $('#Invoices_customer').select2({
        minimumResultsForSearch: 1
    });
});

/**
 * Invoice class
 */
class Invoice {
    /**
     * Update the total with taxes value
     */
    updateTotalWithTaxes() {
        let price = $('#Invoices_productPrice').val().toNumber();

        $('#Invoices_totalWithTaxes').val(price ? price.toCurrency() : null);
        this.calcTotalWithoutTaxes();
    }

    /**
     * Calculate price without taxes
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
