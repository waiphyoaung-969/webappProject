(() => {
    const dialog = document.querySelector('[data-product-dialog]');
    if (!dialog) return;
    const form = dialog.querySelector('[data-product-form]');
    const toast = document.querySelector('[data-toast]');
    let selectedProduct = null;
    const showToast = (message) => { toast.textContent = message; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 3500); };
    const openDialog = (product = null) => {
        selectedProduct = product;
        form.reset();
        dialog.querySelector('[data-dialog-title]').textContent = product ? 'Edit product' : 'Add product';
        if (product) Object.entries(product).forEach(([key, value]) => { if (form.elements[key]) form.elements[key].value = value ?? ''; });
        dialog.showModal();
    };
    document.querySelector('[data-open-create]')?.addEventListener('click', () => openDialog());
    document.querySelectorAll('[data-edit-product]').forEach(button => button.addEventListener('click', () => openDialog(JSON.parse(button.closest('[data-product-row]').dataset.product))));
    document.querySelector('[data-close-dialog]')?.addEventListener('click', () => dialog.close());
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.price = Number(payload.price);
        payload.stock = Number(payload.stock);
        const url = selectedProduct ? `https://dummyjson.com/products/${selectedProduct.id}` : 'https://dummyjson.com/products/add';
        try {
            const response = await fetch(url, { method: selectedProduct ? 'PUT' : 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
            if (!response.ok) throw new Error('Request failed');
            dialog.close();
            showToast('DummyJSON accepted this demo change. It is not permanently saved.');
        } catch (error) { showToast('Could not save the product. Please try again.'); }
    });
    document.querySelector('[data-select-all]')?.addEventListener('change', event => document.querySelectorAll('tbody input[type="checkbox"]').forEach(input => input.checked = event.target.checked));
    document.addEventListener('keydown', event => { if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); document.querySelector('input[name="q"]')?.focus(); } });
})();
