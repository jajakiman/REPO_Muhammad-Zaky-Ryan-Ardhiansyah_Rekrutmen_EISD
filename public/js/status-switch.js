(function () {
    'use strict';

    var container = document.querySelector('[data-toast-container]');
    if (!container) return;

    function showToast(message, error) {
        var toast = document.createElement('div');
        toast.className = 'flex w-full max-w-sm items-start gap-3 rounded-xl border px-4 py-3 text-sm font-semibold shadow-lg ' + (error ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800');
        toast.setAttribute('role', error ? 'alert' : 'status');
        toast.textContent = message;
        container.appendChild(toast);
        window.setTimeout(function () { toast.remove(); }, 3000);
    }

    document.querySelectorAll('[data-status-switch]').forEach(function (form) {
        var checkbox = form.querySelector('input[type="checkbox"]');
        var label = form.querySelector('[data-status-label]');

        checkbox.addEventListener('change', function (event) {
            event.preventDefault();
            var nextState = checkbox.checked;
            checkbox.disabled = true;

            fetch(form.action, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ is_active: nextState })
            }).then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Status gagal diperbarui.');
                    label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                    showToast(data.message, false);
                });
            }).catch(function (error) {
                checkbox.checked = !nextState;
                showToast(error.message, true);
            }).finally(function () {
                checkbox.disabled = false;
            });
        });
    });
})();
