/**
 * Init safe detail
 */
$(document).ready(function () {
    'use strict';

    new Safe();
});

/**
 * Safe class
 */
class Safe {
    /**
     * Construct safe class
     */
    constructor() {
        this.init();
    }

    /**
     * Initialize safe class
     */
    init() {
        this.calcAllTotals();
        this.calcAllBalances();
    }

    /**
     * Calculate all totals for this safe
     */
    calcAllTotals() {
        let length = $('#safes-datatable thead tr th:not(:first, :last)').length;

        for (let i = 1; i <= length; i++) {
            this.calcTotalForColumn(i + 1);
        }
    }

    /**
     * Calculate all balances for this safe
     */
    calcAllBalances() {
        let length = $('#safes-datatable tbody tr').length;

        for (let i = 1; i <= length; i++) {
            this.calcBalanceForRow(i);
        }

        this.displayLastBalance();
    }

    /**
     * Calculate the total for a given column
     * @param column
     */
    calcTotalForColumn(column) {
        let total = 0;

        $('#safes-datatable tbody tr td:nth-child(' + column + ') a.text-reset').each(function () {
            total += $(this).text().toNumber();
        });

        $('#safes-datatable tfoot tr th:nth-child(' + column + ')').text(total ? total.toCurrency() : '0,00 €');
        this.colorColumn(column, total);
    }

    /**
     * Calculate the balance for a given row
     * @param row
     */
    calcBalanceForRow(row) {
        let total = 0;

        $('#safes-datatable tbody tr:nth-child(' + row + ') a.text-reset:not(:last)').each(function () {
            total += $(this).text().toNumber();
        });

        let safesControlItem = $('#safes-datatable tbody tr:nth-child(' + row + ') td:last a.text-reset');
        let safesControl = safesControlItem.text().toNumber();
        let previousBalance = $('#safes-datatable tbody tr:nth-child(' + (row - 1) + ') td:last a.text-reset').text().toNumber();
        previousBalance = !previousBalance
            ? $('#safes-datatable tbody tr:nth-child(' + (row - 1) + ') td:last').text().toNumber()
            : previousBalance;
        let balance = !safesControl
            ? previousBalance + total
            : safesControl + total;

        !safesControl
            ? $('#safes-datatable tbody tr:nth-child(' + row + ') td:last').text(balance.toCurrency())
            : safesControlItem.text(balance.toCurrency()).parent().addClass('fw-bold');

        this.colorBalance(row, balance);
    }

    /**
     * Display the last balance in table footer
     */
    displayLastBalance() {
        let lastBalance = $('#safes-datatable tbody tr:last td:last a.text-reset').text().toNumber();
        lastBalance = !lastBalance
            ? $('#safes-datatable tbody tr:last td:last').text().toNumber()
            : lastBalance;

        $('#safes-datatable tfoot tr th:last').text(lastBalance ? lastBalance.toCurrency() : '0,00 €');
        this.colorColumn($('#safes-datatable tfoot tr th').length, lastBalance);
    }

    /**
     * Color the total for a given column
     * @param column
     * @param total
     */
    colorColumn(column, total) {
        let color = this.getColor(total);

        $('#safes-datatable tfoot tr th:nth-child(' + column + ')').addClass(color);
    }

    /**
     * Color the balance for a given row
     * @param row
     * @param total
     */
    colorBalance(row, total) {
        let color = this.getColor(total);

        $('#safes-datatable tbody tr:nth-child(' + row + ') td:last').addClass(color);
    }

    /**
     * Get the color for a currency
     * @param total
     * @returns {string}
     */
    getColor(total) {
        let color;

        switch (true) {
            case (total > 0):
                color = 'text-success';
                break;
            case (total < 0):
                color = 'text-danger';
                break;
        }

        return color;
    }
}

/**
 * Convert currency to number
 * * @returns {number}
 */
String.prototype.toNumber = function() {
    return Number(this.replace(/[^0-9-]+/g,'')) / 100;
};

/**
 * Convert number to currency
 * @returns {string}
 */
Number.prototype.toCurrency = function() {
    return new Intl.NumberFormat($('html').attr('lang'), {style:'currency', currency: 'EUR'}).format(this);
};
