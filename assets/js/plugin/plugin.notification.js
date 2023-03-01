/**
 * Import notification css
 */
import('jquery-toast-plugin/dist/jquery.toast.min.css');

/**
 * Import notification js
 */
require('jquery-toast-plugin');

/**
 * Init notification plugin
 */
!function (c) {
    function t() {}
    t.prototype.send = function (t, o, i, e, n, a, s, r) {
        t = {heading: t, text: o, position: i, loaderBg: e, icon: n, hideAfter: a = a || 3e3, stack: s = s || 1};
        t.showHideTransition = r || "fade", c.toast().reset("all"), c.toast(t)
    }, c.NotificationApp = new t, c.NotificationApp.Constructor = t
}(window.jQuery);

$(document).ready(function () {
    'use strict';

    $('.flash-message-item').each(function () {
        $.NotificationApp.send($(this).attr('data-title'),
            $(this).text(),
            'top-right',
            'rgba(0, 0, 0, 0.2)',
            $(this).attr('data-status') === 'danger' ? 'error' : $(this).attr('data-status'));
    });
    $('#flash-message').remove();
});
