(() => {
    const dialog = document.querySelector('[data-product-dialog]');
    if (!dialog) return;

    const form = dialog.querySelector('[data-product-form]');
    const toast = document.querySelector('[data-toast]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let selectedProduct = null;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
    const showToast = (message, isError = false) => {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.toggle('toast-error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    };
    const productRow = (product) => {
        const id = Number(product.id);
        const title = escapeHtml(product.title);
        const brand = escapeHtml(product.brand || String(product.category || 'Product').replaceAll('-', ' '));
        const category = escapeHtml(String(product.category || '').replaceAll('-', ' '));
        const thumbnail = escapeHtml(product.thumbnail || 'https://placehold.co/80x80/e4f7ff/4695b5?text=Item');
        const stock = Number(product.stock || 0);
        const price = (Number(product.price) || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const rating = (Number(product.rating) || 0).toFixed(1);
        const encoded = escapeHtml(JSON.stringify(product));
        return `<tr data-product-row data-product-id="${id}" data-product='${encoded}'>
            <td><input type="checkbox" aria-label="Select ${title}"></td>
            <td><a class="product-cell" href="/products/${id}"><img src="${thumbnail}" alt="" loading="lazy" onerror="this.src='https://placehold.co/80x80/e4f7ff/4695b5?text=Item'"><span><strong>${title}</strong><small>${brand}</small></span></a></td>
            <td><span class="pill ${stock > 0 ? 'pill-green' : 'pill-red'}"><i></i>${stock > 0 ? 'In stock' : 'Out of stock'}</span></td>
            <td class="price">$${price}</td><td><span class="category-tag">${category}</span></td>
            <td><span class="rating">★</span> ${rating}</td>
            <td class="row-actions"><button class="more-button" type="button" aria-label="Edit ${title}" data-edit-product>Edit</button><button class="delete-button" type="button" aria-label="Delete ${title}" data-delete-product>Delete</button></td>
        </tr>`;
    };
    const updateCounts = () => {
        const count = document.querySelectorAll('[data-product-row]').length;
        document.querySelectorAll('.result-count').forEach((element) => element.textContent = count);
        const footer = document.querySelector('.table-footer > span');
        if (footer) footer.innerHTML = `Showing <strong>${count ? 1 : 0}–${count}</strong> of <strong>${count}</strong> products`;
        const stat = document.querySelector('#insights .stat-value');
        if (stat) stat.textContent = count.toLocaleString();
        const sidebarCount = document.querySelector('.nav-count');
        if (sidebarCount) sidebarCount.textContent = count;
    };
    const openDialog = (product = null) => {
        selectedProduct = product;
        form.reset();
        dialog.querySelector('[data-dialog-title]').textContent = product ? 'Edit product' : 'Add product';
        if (product) Object.entries(product).forEach(([key, value]) => {
            if (form.elements[key]) form.elements[key].value = value ?? '';
        });
        dialog.showModal();
    };
    const requestHeaders = {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf};
    const responseJson = async (response) => {
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const message = result.message || Object.values(result.errors || {}).flat()[0] || 'Please check the product details and try again.';
            throw new Error(message);
        }
        return result;
    };

    document.querySelector('[data-open-create]')?.addEventListener('click', () => openDialog());
    document.querySelector('[data-close-dialog]')?.addEventListener('click', () => dialog.close());

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.price = Number(payload.price);
        payload.stock = Number(payload.stock);
        for (const key of ['brand', 'thumbnail', 'description']) if (!payload[key]) delete payload[key];
        const url = selectedProduct ? `${form.dataset.updateBase}/${selectedProduct.id}` : form.dataset.createUrl;

        try {
            const result = await responseJson(await fetch(url, {
                method: selectedProduct ? 'PUT' : 'POST',
                headers: requestHeaders,
                body: JSON.stringify(payload)
            }));
            const product = result.product;
            if (!product?.id) throw new Error('The catalog did not return the saved product.');
            const existing = selectedProduct && document.querySelector(`[data-product-id="${Number(selectedProduct.id)}"]`);
            if (existing) existing.outerHTML = productRow(product);
            else document.querySelector('tbody')?.insertAdjacentHTML('afterbegin', productRow(product));
            updateCounts();
            dialog.close();
            showToast(selectedProduct ? 'Product updated. DummyJSON demo changes are temporary.' : 'Product added. DummyJSON demo changes are temporary.');
        } catch (error) {
            showToast(error.message || 'Could not save the product. Please try again.', true);
        }
    });

    document.querySelector('tbody')?.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-edit-product]');
        if (editButton) {
            const editRow = editButton.closest('[data-product-row]');
            try { openDialog(JSON.parse(editRow.dataset.product)); }
            catch { showToast('Could not read this product. Refresh the page and try again.', true); }
            return;
        }
        const button = event.target.closest('[data-delete-product]');
        if (!button) return;
        const row = button.closest('[data-product-row]');
        const product = JSON.parse(row.dataset.product);
        if (!window.confirm(`Delete “${product.title}” from this catalog view?`)) return;
        button.disabled = true;
        try {
            await responseJson(await fetch(`${form.dataset.deleteBase}/${product.id}`, {
                method: 'DELETE',
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrf}
            }));
            row.remove();
            updateCounts();
            showToast('Product removed. DummyJSON demo changes are temporary.');
        } catch (error) {
            button.disabled = false;
            showToast(error.message || 'Could not delete the product. Please try again.', true);
        }
    });

    document.querySelector('[data-select-all]')?.addEventListener('change', (event) => {
        document.querySelectorAll('tbody input[type="checkbox"]').forEach((input) => input.checked = event.target.checked);
    });
    document.addEventListener('keydown', (event) => {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            document.querySelector('input[name="q"]')?.focus();
        }
    });
})();

