/**
 * Init control form
 */
$(document).ready(function () {
    'use strict';

    let control = new Control();

    $('#Controls_counter, #Controls_period').on('change', function () {
        control.init();
    });

    $('input').on('change', function () {
        control.calcResult();
    });

    $('#new-Controls-form, #edit-Controls-form').on('submit', function (event) {
        control.checkEmptyRequiredField(event);
    });
});

/**
 * Control class
 */
class Control {
    /**
     * Construct controls class
     */
    constructor() {
        this.defaultTurnover = $('#Controls_turnover').val();
        this.defaultCashFund = $('#Controls_cashFund').val();
        this.defaultCounter = $('#Controls_counter').find(':selected').val();

        this.init();
    }

    /**
     * Initialize controls class
     */
    init() {
        this.loadPeriod();
        this.loadCounter();
        this.calcResult();
    }

    /**
     * Load the period for control
     */
    loadPeriod() {
        let isDebit = (typeof $('#Controls_period').find(':selected').attr('data-debit') !== 'undefined');

        $('#Controls_turnover').attr('readonly', !isDebit).val(isDebit ? this.defaultTurnover : null);
    }

    /**
     * Load the counter for control
     */
    loadCounter() {
        let counterElement = $('#Controls_counter').find(':selected');
        let counter = counterElement.val();
        let cashFund = counterElement.attr('data-cash-fund') / 100;

        $('#Controls_cashFund').val(this.defaultCashFund && this.defaultCounter === counter
            ? this.defaultCashFund
            : cashFund ? cashFund.toCurrency() : null);
    }

    /**
     * Calculate the result for control
     */
    calcResult() {
        let result = 0;
        let turnover = $('#Controls_turnover').val().toNumber();
        let cashFund = $('#Controls_cashFund').val().toNumber();

        $('input[data-calc]').each(function () {
            result += $(this).val() * $(this).attr('data-calc');
        });

        $('#Controls_result').val(result ? result.toCurrency() : '');
        this.calcError(result, turnover ? turnover : 0, cashFund ? cashFund : 0);
        this.calcDebit(result, cashFund ? cashFund : 0);
    }

    /**
     * Calculate the error for control
     * @param result
     * @param turnover
     * @param cashFund
     */
    calcError(result, turnover, cashFund) {
        let reverse = (typeof $('#Controls_counter').find(':selected').attr('data-reverse') !== 'undefined');
        let error = !reverse ? result - (turnover + cashFund) : result + (turnover - cashFund);

        $('#Controls_error').val(turnover || cashFund ? error.toCurrency() : null);
        this.colorError(error);
    }

    /**
     * Calculate the debit for control
     * @param result
     * @param cashFund
     */
    calcDebit(result, cashFund) {
        $('#debit-span').text((result - cashFund).toCurrency());
        this.displayDebit( result - cashFund);
    }

    /**
     * Calculate the error for control
     * @param error
     */
    colorError(error) {
        let addClass, removeClass;

        switch (true) {
            case (error > 0):
                addClass = 'text-success';
                removeClass = 'text-danger';
                break;
            case (error < 0):
                addClass = 'text-danger';
                removeClass = 'text-success';
                break;
        }

        $('#Controls_error').removeClass(removeClass ?? 'text-danger text-success').addClass(addClass);
    }

    /**
     * Display debit alert
     * @param debit
     */
    displayDebit(debit) {
        let isReverse = (typeof $('#Controls_counter').find(':selected').attr('data-reverse') !== 'undefined');
        let isDebit = (typeof $('#Controls_period').find(':selected').attr('data-debit') !== 'undefined');

        $('#debit-alert').css('display', (isDebit && debit) ? 'block' : 'none')
            .removeClass(debit >= 0
                ? !isReverse ? 'alert-danger' : 'alert-primary'
                : !isReverse ? 'alert-primary' : 'alert-danger')
            .addClass(debit >= 0
                ? !isReverse ? 'alert-primary' : 'alert-danger'
                : !isReverse ? 'alert-danger' : 'alert-primary'
        );
        $('.debit-span').text((debit >= 0 ? debit : -debit).toCurrency());
        $('#debit-text-positive').css('display', debit >= 0 ? 'block' : 'none');
        $('#debit-text-negative').css('display', debit >= 0 ? 'none' : 'block');
    }

    /**
     * Check empty required fields
     */
    checkEmptyRequiredField(event) {
        if (!$('#Controls_turnover').val() && !$('#Controls_result').val()) {
            event.preventDefault();
        }
    }
}
