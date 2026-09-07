(function () {
    'use strict';

    function updateCampusField(form) {
        var affiliation = form.querySelector('[data-affiliation-campus]');
        var campusField = form.querySelector('[data-campus-field]');
        if (!affiliation || !campusField) return;

        var campus = campusField.querySelector('select');
        var required = ['student', 'lecturer', 'staff'].indexOf(affiliation.value) !== -1;
        campusField.hidden = affiliation.value === 'visitor';
        campus.required = required;

        if (affiliation.value === 'visitor') campus.value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-validated-form]').forEach(function (form) {
            var affiliation = form.querySelector('[data-affiliation-campus]');
            if (affiliation) {
                updateCampusField(form);
                affiliation.addEventListener('change', function () { updateCampusField(form); });
            }

            form.addEventListener('submit', function (event) {
                if (form.checkValidity()) return;
                event.preventDefault();
                var summary = form.querySelector('[data-client-error-summary]');
                if (summary) summary.hidden = false;
                var invalid = form.querySelector(':invalid');
                if (invalid) invalid.focus();
            });
        });

        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.passwordToggle);
                var visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.setAttribute('aria-label', visible ? 'Tampilkan password' : 'Sembunyikan password');
                button.setAttribute('aria-pressed', String(!visible));
                button.querySelector('[data-eye-open]').classList.toggle('hidden', !visible);
                button.querySelector('[data-eye-closed]').classList.toggle('hidden', visible);
            });
        });
    });
})();
