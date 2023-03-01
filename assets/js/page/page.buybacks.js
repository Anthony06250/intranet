/**
 * Init buyback form
 */
$(document).ready(function () {
    'use strict';

    let buyback = new Buyback();

    document.addEventListener('ea.collection.item-added', function () {
        buyback.initCollection();
    });

    document.addEventListener('ea.collection.item-removed', function () {
        buyback.calcStartingPrice();
    });

    $('#Buybacks_increasedPercent').on('change', function () {
        buyback.calcInterests();
    })

    $('#Buybacks_increasedPrice').on('change', function () {
        buyback.calcPercent();
    })

    $('#Buybacks_createdAt, #Buybacks_duration').on('change', function () {
        buyback.calcDueDate();
    })
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
        this.initCollection();
    }

    /**
     * Init buybacks class
     */
    init() {
        this.calcDueDate();
    }

    /**
     * Init buybacks collection
     */
    initCollection() {
        let buyback = this;

        $('.field-collection-item input[id$=\'_price\']').on('change', function () {
            let value = $(this).val().toNumber();

            $(this).val(value ? value.toCurrency() : null);
            buyback.calcStartingPrice();
        });
    }

    /**
     * Calculate the starting price
     */
    calcStartingPrice() {
        let total = 0;

        $('.field-collection-item input[id$=\'_price\']').each(function () {
            total += $(this).val().toNumber();
        });

        $('#Buybacks_startingPrice').val(total ? total.toCurrency() : null);
        this.calcInterests();
    }

    /**
     * Calculate buyback interests
     */
    calcInterests() {
        let price = $('#Buybacks_startingPrice').val().toNumber();
        let percent = $('#Buybacks_increasedPercent').val().toNumber() / 100;

        $('#Buybacks_increasedPrice').val(price ? (price + (price * percent)).toCurrency() : null);
    }

    /**
     * Calculate increase percent
     */
    calcPercent() {
        let starting_price = $('#Buybacks_startingPrice').val().toNumber();
        let increased_price = $('#Buybacks_increasedPrice').val().toNumber();
        let percent = (increased_price - starting_price) / starting_price * 100;

        $('#Buybacks_increasedPercent').val((increased_price ? percent : 30).toCurrency());
    }

    /**
     * Calculate buyback due date
     */
    calcDueDate() {
        let start_date = new Date($('#Buybacks_createdAt').val());
        let duration = parseInt($('#Buybacks_duration').val());

        if (!isNaN(start_date) && !isNaN(duration)) {
            start_date.setDate(start_date.getDate() + duration);
            $('#Buybacks_dueAt').val(start_date.toInputFormat());
        }
        else {
            $('#Buybacks_dueAt').val(null);
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
