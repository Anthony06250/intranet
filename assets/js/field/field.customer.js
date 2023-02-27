/**
 * Init customer field
 */
$(document).ready(function () {
    'use strict';

    let customer = new Customer();

    $('select[id$="_customer"]').select2({
        minimumResultsForSearch: 1,
        templateSelection: customer.formatCustomerSelection
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
            customer.preSubmitNewCustomer();
        }
    });
});

/**
 * Customer class
 */
class Customer {
    /**
     * Construct customer class
     */
    constructor() {
        this.newUrl = '/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CCustomersCrudController';
        this.formPrototype = null;
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
            url: '/' + $('html').attr('lang') + this.newUrl,
            type: 'GET',
            async: true,

            success: function(data) {
                customer.formPrototype = data;
                customer.displayAddButton();
            },
            error : function() {
                console.log('Fail to request new customer form');
            }
        });
    }

    /**
     * Format customer selection list with edit button
     * @param customer
     * @returns {*|jQuery|HTMLElement}
     */
    formatCustomerSelection(customer) {
        if (customer.id) {
            let editUrl = '/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCustomersCrudController&entityId=';
            let url = '/' + $('html').attr('lang') + editUrl + customer.id;
            let editButton = '<a href="' + url + '" target="_blank" class="btn btn-outline-warning float-end py-0 px-1 mt-1 me-3">\n' +
                '<i class="uil-pen"></i>\n' +
                '</a>';

            return $('<span>' + customer.text + editButton + '</span>');
        }

        return customer.text;
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
        let customer = this;

        $(this.formPrototype).insertBefore('select[id$="_customer"]');
        $('#new-Customers-form .select2').select2({
            minimumResultsForSearch: Infinity
        });
        $('#button-select-customer').on('click', function () {
            customer.selectExistingCustomer(customer.isExistCustomer());
        });
    }

    /**
     * Make verification before submitting a new customer
     */
    preSubmitNewCustomer() {
        if (this.isValidCustomer()) {
            if (this.isExistCustomer()) {
                return this.showExistingCustomerModal();
            }

            return this.submitNewCustomer();
        }
    }

    /**
     * Submit new customer form with ajax
     */
    submitNewCustomer() {
        let customer = this;

        $.ajax({
            url: '/' + $('html').attr('lang') + this.newUrl,
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
     * Check if the new customer already exist
     * @returns {boolean}
     */
    isExistCustomer()
    {
        let customerName = this.getNewCustomerName();
        let response = null;

        $('select[id$="_customer"] option').each(function () {
            if ($(this).text() === customerName) {
                response = $(this).val();
            }
        });

        return response;
    }

    /**
     * Show alert modal if customer already exist
     */
    showExistingCustomerModal() {
        let modalBodyElement = $('#modal-body-customer');

        modalBodyElement.html(modalBodyElement.html().replace('__FULLNAME__', this.getNewCustomerName()));
        $('#customer-edit-modal').modal('show');
    }

    /**
     * Select an existing customer in customer list
     * @param customerId
     */
    selectExistingCustomer(customerId) {
        $('select[id$="_customer"]').val(customerId).change();
        this.removeNewCustomer();
        this.submitParentForm();
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
        $('button[class*="action-save"]').click();
    }
}

/**
 * Capitalize the first letter
 * @returns {string}
 */
String.prototype.ucFirst = function () {
    return this.replace(/^(.)|\s(.)/g, function($1) {return $1.toUpperCase();});
};
