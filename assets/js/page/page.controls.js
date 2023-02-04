/**
 * Init control form
 */
$(document).ready(function () {
    'use strict';

    let control = new Control();

    $('#Controls_controlsCounter').on('change', function () {
        control.loadCounter(true);
    });

    $('input, #Controls_controlsPeriod').on('change', function () {
        control.loadPeriod();
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
        this.init();
    }

    /**
     * Initialize controls class
     */
    init() {
        this.loadCounter();
    }

    /**
     * Load the counter
     */
    loadCounter(force = false) {
        if (!$('#Controls_cash_fund').val() || force) {
            this.updateCashFund();
        }

        this.calcResult();
    }

    loadPeriod() {
        let isDebit = (typeof $('#Controls_controlsPeriod').find(':selected').attr('data-debit') !== 'undefined');

        $('#Controls_turnover').attr('readonly', !isDebit).val(null);

        this.calcResult();
    }

    /**
     * Load the cash fund default value
     */
    updateCashFund() {
        let cash_fund = $('#Controls_controlsCounter').find(':selected').attr('data-cash-fund') / 100;

        $('#Controls_cash_fund').val(cash_fund ? cash_fund.toCurrency() : null);
    }

    /**
     * Calculate the result for control
     */
    calcResult() {
        let result = 0;
        let turnover = $('#Controls_turnover').val().toNumber();
        let cash_fund = $('#Controls_cash_fund').val().toNumber();

        $('input[data-calc]').each(function () {
            result += $(this).val() * $(this).attr('data-calc');
        });

        $('#Controls_result').val(result ? result.toCurrency() : '');
        this.calcError(result, turnover ? turnover : 0, cash_fund ? cash_fund : 0);
        this.calcDebit(result, cash_fund ? cash_fund : 0);
    }

    /**
     * Calculate the error fo control
     * @param result
     * @param turnover
     * @param cash_fund
     */
    calcError(result, turnover, cash_fund) {
        let reverse = (typeof $('#Controls_controlsCounter').find(':selected').attr('data-reverse') !== 'undefined');
        let error = !reverse ? result - (turnover + cash_fund) : result + (turnover - cash_fund);

        $('#Controls_error').val(turnover || cash_fund ? error.toCurrency() : null);
        this.colorError(error);
    }

    /**
     * Calculate the debit for control
     * @param result
     * @param cash_fund
     */
    calcDebit(result, cash_fund) {
        $('#debit-span').text((result - cash_fund).toCurrency());
        this.displayDebit( result - cash_fund);
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
        let isReverse = (typeof $('#Controls_controlsCounter').find(':selected').attr('data-reverse') !== 'undefined');
        let isDebit = (typeof $('#Controls_controlsPeriod').find(':selected').attr('data-debit') !== 'undefined');

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
        if (!$('#Controls_turnover').val()
            && !$('#Controls_result').val()) {
            event.preventDefault();
        }
    }
}
