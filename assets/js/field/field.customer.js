/**
 * Init customer field
 */
$(document).ready(function () {
    'use strict';

    let customer = new Customer();

    $('select[id$="_customer"]').select2({
        minimumResultsForSearch: 1
    });

    $('button[class*="field-customer-add-button"]').on('click', function () {
        customer.createNewCustomer();
    });

    $('button[class*="field-customer-remove-button"]').on('click', function () {
        customer.removeNewCustomer();
    });

    $('button[class*="action-save"]').on('click', function (event) {
        if ($('#new-Customers-form').length) {
            event.preventDefault();
            customer.submitNewCustomer();
        }
    });
});

/**
 * Customer class
 */
class Customer {
    url = '/' + $('html').attr('lang') + '/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CCustomersCrudController';
    form_prototype = null;

    /**
     * Construct customer class
     */
    constructor() {
        this.init();
    }

    /**
     * Init customer class
     */
    init() {
        this.getFormPrototype();
    }

    /**
     * Get new customer form with ajax
     */
    getFormPrototype() {
        let customer = this;

        $.ajax({
            url: this.url,
            type: 'GET',
            async: true,

            success: function(data) {
                customer.form_prototype = data;
                customer.displayAddButton();
            },
            error : function() {
                console.log('Fail to request new customer form');
            }
        });
    }

    /**
     * Show new customer form
     */
    createNewCustomer() {
        this.displayRemoveButton();
        $('select[id$="_customer"]').addClass('d-none').next().addClass('d-none');
        this.generateNewCustomerForm();
    }

    /**
     * Remove new customer form
     */
    removeNewCustomer() {
        this.displayAddButton();
        $('select[id$="_customer"]').removeClass('d-none').next().removeClass('d-none');
        $('#new-Customers-form').remove();
    }

    /**
     * Display add customer button
     */
    displayAddButton() {
        $('button[class*="field-customer-add-button"]').removeClass('d-none');
        $('button[class*="field-customer-remove-button"]').addClass('d-none');
    }

    /**
     * Display remove customer button
     */
    displayRemoveButton() {
        $('button[class*="field-customer-add-button"]').addClass('d-none');
        $('button[class*="field-customer-remove-button"]').removeClass('d-none');
    }

    /**
     * Generate new customer form
     */
    generateNewCustomerForm() {
        $(this.form_prototype).insertBefore('select[id$="_customer"]');
        $('#new-Customers-form .select2').select2({
            minimumResultsForSearch: Infinity
        });
    }

    /**
     * Submit new customer form with ajax
     */
    submitNewCustomer() {
        if (this.isValidCustomer()) {
            let customer = this;

            $.ajax({
                url: this.url,
                type: 'POST',
                data: $('#new-Customers-form').serialize(),

                success: function (data) {
                    customer.updateCustomersList(data);
                    customer.submitParentForm();
                },
                error: function() {
                    console.log('Fail to create new customer');
                }
            });
        }
    }

    /**
     * Check if is valid customer
     * @returns {boolean}
     */
    isValidCustomer() {
        return !!($('#Customers_civility').find(':selected').val()
            && $('#Customers_firstname').val()
            && $('#Customers_lastname').val());
    }

    /**
     * Update customer list after submit new customer
     * @param customerId
     */
    updateCustomersList(customerId) {
        let option = new Option(this.getNewCustomerName(), customerId, false, true);

        $('select[id$="_customer"]').append(option).trigger('change');
        this.removeNewCustomer();
    }

    /**
     * Get new customer fullname
     * @returns {string}
     */
    getNewCustomerName() {
        let civility = $('#Customers_civility').find(':selected');

        return [
            civility.val() ? civility.text() : '',
            $('#Customers_firstname').val().ucFirst(),
            $('#Customers_lastname').val().toUpperCase()
        ].join(' ').trim();
    }

    /**
     * Submit parent form after create new customer
     */
    submitParentForm() {
        $('button[class*="action-save"]').unbind('click').click();
    }
}

/**
 * Capitalize the first letter
 * @returns {string}
 */
String.prototype.ucFirst = function () {
    return this.replace(/^(.)|\s(.)/g, function($1) {return $1.toUpperCase();});
};
