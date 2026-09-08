(function () {
    'use strict';

    var menu = document.querySelector('[data-user-menu]');
    if (!menu) return;

    var button = menu.querySelector('[data-user-menu-button]');
    var panel = menu.querySelector('[data-user-menu-panel]');

    function setOpen(open) {
        panel.classList.toggle('hidden', !open);
        button.setAttribute('aria-expanded', String(open));
    }

    button.addEventListener('click', function () {
        setOpen(button.getAttribute('aria-expanded') !== 'true');
    });
    document.addEventListener('click', function (event) {
        if (!menu.contains(event.target)) setOpen(false);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            button.focus();
        }
    });
})();
