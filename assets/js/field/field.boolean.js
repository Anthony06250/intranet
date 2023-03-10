/**
 * Init boolean field
 */
(() => {
    function e(e, t) {
        for (var n = 0; n < t.length; n++) {
            var i = t[n];
            i.enumerable = i.enumerable || !1, i.configurable = !0, 'value' in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
        }
    }

    function t(t, n, i) {
        return n && e(t.prototype, n), i && e(t, i), Object.defineProperty(t, 'prototype', {writable: !1}), t
    }

    function n(e, t) {
        !function (e, t) {
            if (t.has(e)) throw new TypeError('Cannot initialize the same private elements twice on an object')
        }(e, t), t.add(e)
    }

    function i(e, t, n) {
        if (!t.has(e)) throw new TypeError('attempted to get private field on non-instance');
        return n
    }

    document.addEventListener('DOMContentLoaded', (function () {
        document.querySelectorAll('td.field-boolean .form-switch input[type="checkbox"]').forEach((function (e) {
            new a(e)
        }))
    }));
    var o = new WeakSet, r = new WeakSet, a = t((function e(t) {
        'use strict';

        !function (e, t) {
            if (!(e instanceof t)) throw new TypeError('Cannot call a class as a function')
        }(this, e), n(this, r), n(this, o), this.field = t, this.field.addEventListener('change', i(this, o, c).bind(this))
    }));

    function c() {
        var e = this, t = this.field.checked,
            n = this.field.getAttribute('data-toggle-url') + '&newValue=' + t.toString();
        fetch(n, {method: 'PATCH', headers: {'X-Requested-With': 'XMLHttpRequest'}}).then((function (t) {
            return t.ok || i(e, r, s).call(e), t.text(), location.reload();
        })).then((function () {
        })).catch((function () {
            return i(e, r, s).call(e)
        }))
    }

    function s() {
        this.field.checked = !this.field.checked, this.field.disabled = !0, this.field.closest(".form-switch").classList.add('disabled')
    }
})();
