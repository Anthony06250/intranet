/**
 * Import select2 css
 */
import('select2/dist/css/select2.min.css');

/**
 * Import select2 js
 */
require('select2');

/**
 * Init select2 plugin
 */
$(document).ready(function () {
    'use strict';

    $('[data-toggle="select2"]').select2({
        minimumResultsForSearch: Infinity
    });
});
