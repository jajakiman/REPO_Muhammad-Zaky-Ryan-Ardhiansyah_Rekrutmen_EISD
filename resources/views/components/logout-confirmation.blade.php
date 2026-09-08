<dialog data-logout-dialog aria-labelledby="logout-dialog-title" aria-describedby="logout-dialog-message" class="fixed inset-0 m-auto max-h-[calc(100dvh-2rem)] w-[min(calc(100%-2rem),28rem)] overflow-y-auto rounded-2xl border-0 bg-white p-0 shadow-2xl backdrop:bg-slate-950/70">
    <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-800" aria-hidden="true">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        </div>
        <h2 id="logout-dialog-title" class="mt-4 text-xl font-bold text-slate-950">Konfirmasi Keluar</h2>
        <p id="logout-dialog-message" class="mt-2 text-sm leading-6 text-slate-600">Apakah Anda yakin ingin keluar dari AksesLoka?</p>
        <div class="mt-6 flex justify-center gap-3">
            <button data-logout-cancel class="button button-secondary" type="button" autofocus>Batal</button>
            <button data-logout-confirm class="button bg-red-700 text-white hover:bg-red-800" type="button">Ya, Keluar</button>
        </div>
    </div>
</dialog>
<script>
    (function () {
        var dialog = document.querySelector('[data-logout-dialog]');
        var pendingForm;

        document.querySelectorAll('[data-logout-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed) return;
                event.preventDefault();
                pendingForm = form;
                dialog.showModal();
            });
        });

        dialog.querySelector('[data-logout-cancel]').addEventListener('click', function () {
            dialog.close();
        });
        dialog.querySelector('[data-logout-confirm]').addEventListener('click', function () {
            if (!pendingForm) return;
            pendingForm.dataset.confirmed = 'true';
            pendingForm.requestSubmit();
        });
    })();
</script>
