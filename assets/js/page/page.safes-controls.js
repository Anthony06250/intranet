/**
 * Init safes control form
 */
$(document).ready(function () {
    'use strict';

    let safesControl = new SafesControl();

    $('#SafesControls_controlsCounters').on('change', function () {
        safesControl.calcTotal();
    });

    $('input').on('change', function () {
        safesControl.calcResult();
    });
});

/**
 * SafesControl class
 */
class SafesControl {
    /**
     * Construct safes controls class
     */
    constructor() {
        this.init();
    }

    /**
     * Initialize safes controls class
     */
    init() {
        this.calcResult();
    }

    /**
     * Calculate the result for safes control
     */
    calcResult() {
        let result = 0;

        $('input[data-calc]').each(function () {
            result += $(this).val() * $(this).attr('data-calc');
        });

        $('#SafesControls_result').val(result ? result.toCurrency() : null);
        this.calcTotal();
    }

    /**
     * Calculate the total for safes control
     */
    calcTotal() {
        let counters = 0;
        let result = $('#SafesControls_result').val().toNumber();

        $('#SafesControls_controlsCounters').find(':selected').each(function () {
            counters += $(this).attr('data-cash-fund').toNumber() / 100;
        });

        $('#SafesControls_total').val(counters || result ? (counters + result).toCurrency() : null);
    }
}
