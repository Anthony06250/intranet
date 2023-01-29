/**
 * Init buyback form
 */
$(document).ready(function () {
    'use strict';

    let buyback = new Buyback();

    $('#Buybacks_starting_price, #Buybacks_increased_percent').on('change', function () {
        buyback.calcInterests();
    })

    $('#Buybacks_increased_price').on('change', function () {
        buyback.calcPercent();
    })

    $('#Buybacks_created_at, #Buybacks_duration').on('change', function () {
        buyback.calcDueDate();
    })

    $('#Buybacks_customer').select2({
        minimumResultsForSearch: 1
    });
});

/**
 * Buybacks class
 */
class Buyback {
    /**
     * Construct buybacks class
     */
    constructor () {
        this.init();
    }

    /**
     * Init buybacks class
     */
    init() {
        this.calcDueDate();
    }

    /**
     * Calculate buyback interests
     */
    calcInterests() {
        let price = $('#Buybacks_starting_price').val().toNumber();
        let percent = $('#Buybacks_increased_percent').val().toNumber() / 100;

        $('#Buybacks_increased_price').val(price ? (price + (price * percent)).toCurrency() : null);
    }

    /**
     * Calculate increase percent
     */
    calcPercent() {
        let starting_price = $('#Buybacks_starting_price').val().toNumber();
        let increased_price = $('#Buybacks_increased_price').val().toNumber();
        let percent = (increased_price - starting_price) / starting_price * 100;

        $('#Buybacks_increased_percent').val((increased_price ? percent : 30).toCurrency());
    }

    /**
     * Calculate buyback due date
     */
    calcDueDate() {
        let start_date = new Date($('#Buybacks_created_at').val());
        let duration = parseInt($('#Buybacks_duration').val());

        if (!isNaN(start_date) && !isNaN(duration)) {
            start_date.setDate(start_date.getDate() + duration);
            $('#Buybacks_due_at').val(start_date.toInputFormat());
        }
        else {
            $('#Buybacks_due_at').val(null);
        }
    }
}

/**
 * Format date for input
 * @returns {string}
 */
Date.prototype.toInputFormat = function() {
    let year = this.getFullYear().toString();
    let month = (this.getMonth()+1).toString();
    let day  = this.getDate().toString();

    return year
        + '-' + (month[1] ? month : '0' + month[0])
        + '-' + (day[1] ? day : '0' + day[0]);
};
