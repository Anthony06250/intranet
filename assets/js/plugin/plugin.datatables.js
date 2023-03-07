/**
 * Import datatables css
 */
import('datatables.net-bs5/css/dataTables.bootstrap5.min.css');
import('datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
import('select2/dist/css/select2.min.css');

/**
 * Import datatables js
 */
require('datatables.net');
require('datatables.net-bs5');
require('datatables.net-responsive');
require('datatables.net-responsive-bs5');
require('select2');

/**
 * Init datatables plugin
 */
$(document).ready(function () {
    'use strict';

    new Datatables();
});

/**
 * Datatables class
 */
class Datatables {
    /**
     * Construct datatables class
     */
    constructor() {
        this.translationsUrl = {
            'fr': 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/fr-FR.json',
            'it': 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/it-IT.json',
            'es': 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/es-ES.json',
            'de': 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/de-DE.json'
        };
        this.pageLimit = parseInt($('table[data-page-limit]').attr('data-page-limit') ?? 10);
        this.init();
    }

    /**
     * Init datatables class
     */
    init() {
        let datatables = this;
        let table = $("#index-datatable").DataTable({
            'keys': !0,
            'language': {
                'url': datatables.translationsUrl[$('html').attr('lang')],
                'paginate': {
                    'previous': "<i class='mdi mdi-chevron-left'>",
                    'next': "<i class='mdi mdi-chevron-right'>"
                }
            },
            'pageLength': datatables.pageLimit,
            'lengthMenu': datatables.getLengthMenu(),
            initComplete: function () {
                // Add filters
                datatables.addFilters(this.api());
                // Decorate length menu
                datatables.decorateLengthMenu();
            }
        });

        // Sort datatables
        this.sortDatatables(table);
        // Init action delete
        this.initDeleteAction();
    }

    /**
     * Get array of length menu
     * @returns {(*|number|number|number)[]}
     */
    getLengthMenu() {
        return [
            this.pageLimit,
            this.pageLimit * 2,
            this.pageLimit * 4,
            this.pageLimit * 8
        ];
    }

    /**
     * Add filters for each column
     */
    addFilters(table) {
        let datatables = this;

        table.columns(':not(:last)').every(function () {
            let column = this;
            let columnName = $(column.header()).html().trim();
            let placeholder = '<option value="">' + columnName + '</option>';
            let select = $('<select class="select2 w-100" data-toggle="select2"></select>');

            select.append(placeholder).appendTo($(column.footer()).empty()).select2().on('change', function () {
                let value = $.fn.dataTable.util.escapeRegex($(this).val());

                column.search(value ? value : '', true, false).draw();
            });
            datatables.fillFilter(column, select);
        });
    }

    /**
     * Fill filter for each column
     */
    fillFilter(column, select) {
        column.data().unique().sort().each(function (data) {
            let value = data.replace(/(<([^>]+)>)/ig, '').trim();

            if (value.length && !select.find('option[value="' + value + '"]').length) {
                let option = '<option value="' + value + '">' + value + '</option>';

                select.append(option);
            }
        });
    }

    /**
     * Decorate datatables elements
     */
    decorateLengthMenu() {
        let select = $('<select id="datatable-length" class="select2" data-toggle="select2"></select>');
        let options = $('[name="index-datatable_length"] > option').clone();

        select.append(options).insertAfter('[name="index-datatable_length"]').select2({
            minimumResultsForSearch: Infinity
        }).on('change', function () {
            $('[name="index-datatable_length"]').val($(this).val()).trigger('change');
        });
    }

    /**
     * Sort the datatable fields
     */
    sortDatatables(table) {
        let sortField = $('th[data-sort-direction]');

        if (sortField.length) {
            table.order([sortField.index(), sortField.attr('data-sort-direction')]).draw();
        }
    }

    /**
     * Init delete action for each row
     */
    initDeleteAction() {
        $(".action-delete").on('click', function(event) {
            let button = $(this);

            event.preventDefault();
            $("#modal-delete-button").on('click', function() {
                $("#delete-form").attr('action', button.attr('formaction')).submit();
            })
        });
    }
}
