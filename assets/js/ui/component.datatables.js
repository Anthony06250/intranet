/**
 * Import datatables css
 */
import('datatables.net-bs5/css/dataTables.bootstrap5.min.css');
import('datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

/**
 * Import datatables js
 */
require('datatables.net');
require('datatables.net-bs5');
require('datatables.net-responsive');
require('datatables.net-responsive-bs5');

/**
 * Init datatables component
 */
$(document).ready(function () {
    'use strict';

    let translations_url = {
        'fr': 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/fr-FR.json',
        'it': 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/it-IT.json',
        'es': 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json',
        'de': 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/de-DE.json'
    };
    let page_limit = $('table[data-page-limit]').attr('data-page-limit') ?? 10;
    let table = $("#index-datatable").DataTable({
        'keys': !0,
        'language': {
            'url': translations_url[$('html').attr('lang')],
            'paginate': {
                'previous': "<i class='mdi mdi-chevron-left'>",
                'next': "<i class='mdi mdi-chevron-right'>"
            }
        },
        'pageLength': parseInt(page_limit),
        'lengthMenu': [
            page_limit,
            page_limit * 2,
            page_limit * 4,
            page_limit * 8
        ],
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass('pagination-rounded')
        }
    });

    // Sort datatables
    let sort_field = $('th[data-sort-direction]');

    if (sort_field.length) {
        table
            .order([sort_field.index(), sort_field.attr('data-sort-direction')])
            .draw();
    }

    // Init action delete
    $(".action-delete").on('click', function(event) {
        let button = $(this);
        event.preventDefault();
        $("#modal-delete-button").on('click', function() {
            $("#delete-form").attr('action', button.attr('formaction')).submit();
        })
    });
});
