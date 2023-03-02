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

        for (let column = 1; column <= length; column++) {
            let total = this.calcTotalForColumn(column + 1);

            $('#safes-datatable tfoot tr th:nth-child(' + (column + 1) + ')').text(total ? total.toCurrency() : '0,00 €');
            this.colorColumn(column + 1, total);
        }
    }

    /**
     * Calculate the total for a given column
     * @param column
     * @returns {number}
     */
    calcTotalForColumn(column) {
        let total = 0;

        $('#safes-datatable tbody tr td:nth-child(' + column + ') a.text-reset').each(function () {
            total += $(this).text().toNumber();
        });

        return total;
    }

    /**
     * Calculate all balances for this safe
     */
    calcAllBalances() {
        let length = $('#safes-datatable tbody tr').length;

        for (let row = 1; row <= length; row++) {
            let balance = this.calcBalanceForRow(row);
            let safesControlItem = $('#safes-datatable tbody tr:nth-child(' + row + ') td:last a.text-reset');

            !safesControlItem.text().toNumber()
                ? $('#safes-datatable tbody tr:nth-child(' + row + ') td:last').text(balance.toCurrency())
                : safesControlItem.text(balance.toCurrency()).parent().addClass('fw-bold');
            this.colorBalance(row, balance);
        }

        this.displayLastBalance();
    }

    /**
     * Calculate the balance for a given row
     * @param row
     * @returns {*}
     */
    calcBalanceForRow(row) {
        let total = this.calcTotalForRow(row);
        let safesControlItem = $('#safes-datatable tbody tr:nth-child(' + row + ') td:last a.text-reset');
        let previousBalance = $('#safes-datatable tbody tr:nth-child(' + (row - 1) + ') td:last a.text-reset').text().toNumber();
        previousBalance = !previousBalance
            ? $('#safes-datatable tbody tr:nth-child(' + (row - 1) + ') td:last').text().toNumber()
            : previousBalance;
        let balance = !safesControlItem.text().toNumber()
            ? previousBalance + total
            : safesControlItem.text().toNumber() + total;

        if (balance !== previousBalance) {
            this.displayBalanceErrorForRow(row, balance - previousBalance);
        }

        return balance;
    }

    /**
     * Calculate the total for a given row
     * @param row
     * @returns {number}
     */
    calcTotalForRow(row) {
        let total = 0;

        $('#safes-datatable tbody tr:nth-child(' + row + ') td:not(:last) a.text-reset').each(function () {
            total += $(this).text().toNumber();
        });

        return total;
    }

    /**
     * Display the balance error for a given row
     * @param row
     * @param error
     */
    displayBalanceErrorForRow(row, error) {
        let popoverElement = $('#safes-datatable tbody tr:nth-child(' + row + ') span[data-bs-toggle="tooltip"]');

        if (row === 1 || error === 0) {
            popoverElement.remove();
        }
        else {
            let color = this.getColor(error);
            let tooltipColor = error >= 0 ? 'success-tooltip' : 'danger-tooltip';
            let absoluteError = error >= 0 ? '+' + error.toCurrency() : error.toCurrency();

            popoverElement.attr('data-bs-title', popoverElement.attr('data-bs-title').replace('__ERROR__', absoluteError))
                .attr('data-bs-custom-class', tooltipColor).removeClass('d-none')
                .find('i').addClass(color);
        }
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
