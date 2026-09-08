(function () {
    'use strict';

    var loader = document.querySelector('[data-page-loader]');
    if (!loader) return;

    function show() {
        loader.classList.remove('pointer-events-none', 'opacity-0');
        loader.classList.add('opacity-100');
        loader.setAttribute('aria-hidden', 'false');
    }

    function hide() {
        loader.classList.add('pointer-events-none', 'opacity-0');
        loader.classList.remove('opacity-100');
        loader.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        var url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || url.protocol === 'mailto:' || url.protocol === 'tel:') return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        show();
    });

    document.addEventListener('submit', function (event) {
        if (event.defaultPrevented || event.target.matches('[data-status-switch], [method="dialog"]')) return;
        var dialog = event.target.closest('dialog[open]');
        if (dialog) dialog.close();
        show();
    });

    window.addEventListener('beforeunload', show);
    window.addEventListener('pageshow', hide);
    window.addEventListener('load', hide);
})();
