@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<section class="page-heading">
    <div><div class="eyebrow">CATALOG MANAGEMENT</div><h1>Products</h1><p class="subtitle">Manage and review everything in your store.</p></div>
    <button class="button button-primary" type="button" data-open-create><span>＋</span> Add product</button>
</section>
@if($apiError)<div class="notice notice-error"><strong>Catalog unavailable.</strong> We couldn’t reach DummyJSON. Check your connection and try again. <a href="{{ route('admin.products.index') }}">Retry</a></div>@endif
<section class="stats-grid" id="insights">
    <article class="stat-card"><div class="stat-label">TOTAL PRODUCTS <span class="stat-symbol violet">▦</span></div><div class="stat-value">{{ number_format(count($products)) }}</div><div class="stat-foot">Products in current results</div></article>
    <article class="stat-card"><div class="stat-label">CATEGORIES <span class="stat-symbol orange">◇</span></div><div class="stat-value">{{ count($categories) }}</div><div class="stat-foot">Available in the catalog</div></article>
    <article class="stat-card"><div class="stat-label">AVERAGE RATING <span class="stat-symbol yellow">★</span></div><div class="stat-value">{{ count($products) ? number_format(collect($products)->avg('rating'), 1) : '—' }}<small>/ 5</small></div><div class="stat-foot">Across current results</div></article>
</section>
<section class="catalog-panel" id="categories">
    <div class="panel-heading"><div><h2>All products <span class="result-count">{{ count($products) }}</span></h2><p>Browse and manage your product inventory.</p></div><a class="export-link" href="{{ route('admin.products.index', request()->query()) }}" title="Refresh catalog">↻ <span>Refresh</span></a></div>
    <form class="filters" action="{{ route('admin.products.index') }}" method="GET">
        <label class="search-box"><span>⌕</span><input type="search" name="q" placeholder="Search products..." value="{{ $search }}"><kbd>⌘ K</kbd></label>
        <select name="category" aria-label="Filter by category" onchange="this.form.submit()"><option value="">All categories</option>@foreach($categories as $item)<option value="{{ $item['slug'] ?? $item }}" @selected($category === ($item['slug'] ?? $item))>{{ $item['name'] ?? ucfirst(str_replace('-', ' ', $item)) }}</option>@endforeach</select>
        @if($category || $search)<a class="clear-filter" href="{{ route('admin.products.index') }}">Clear</a>@endif
    </form>
    <div class="table-wrap"><table><thead><tr><th class="check-col"><input type="checkbox" aria-label="Select all products" data-select-all></th><th>PRODUCT</th><th>STATUS</th><th>PRICE</th><th>CATEGORY</th><th>RATING</th><th class="actions-head">ACTIONS</th></tr></thead>
    <tbody>
    @forelse($products as $product)
        <tr data-product-row data-product-id="{{ $product['id'] }}" data-product='@json($product)'><td><input type="checkbox" aria-label="Select {{ $product['title'] }}"></td><td><a class="product-cell" href="{{ route('store.products.show', $product['id']) }}"><img src="{{ $product['thumbnail'] ?? '' }}" alt="" loading="lazy" onerror="this.src='https://placehold.co/80x80/e4f7ff/4695b5?text=Item'"><span><strong>{{ $product['title'] }}</strong><small>{{ $product['brand'] ?? ucfirst($product['category']) }}</small></span></a></td><td><span class="pill {{ ($product['stock'] ?? 0) > 0 ? 'pill-green' : 'pill-red' }}"><i></i>{{ ($product['stock'] ?? 0) > 0 ? 'In stock' : 'Out of stock' }}</span></td><td class="price">${{ number_format($product['price'], 2) }}</td><td><span class="category-tag">{{ ucfirst(str_replace('-', ' ', $product['category'])) }}</span></td><td><span class="rating">★</span> {{ number_format($product['rating'], 1) }}</td><td class="row-actions"><button class="more-button" type="button" aria-label="Edit {{ $product['title'] }}" data-edit-product>Edit</button><button class="delete-button" type="button" aria-label="Delete {{ $product['title'] }}" data-delete-product>Delete</button></td></tr>
    @empty
        <tr><td colspan="7" class="empty-state"><span>⌕</span><strong>No products found</strong><p>Try a different search or category.</p></td></tr>
    @endforelse
    </tbody></table></div>
    <div class="table-footer"><span>Showing <strong>{{ count($products) ? 1 : 0 }}–{{ count($products) }}</strong> of <strong>{{ count($products) }}</strong> products</span><div><button disabled>← Previous</button><button disabled>Next →</button></div></div>
</section>
<dialog class="product-dialog" data-product-dialog><form method="dialog" class="dialog-top"><div><span class="eyebrow">CATALOG ITEM</span><h2 data-dialog-title>Add product</h2></div><button class="close-dialog" aria-label="Close">×</button></form><form class="product-form" data-product-form data-create-url="{{ secure_url('/admin/products') }}" data-update-base="{{ secure_url('/admin/products') }}" data-delete-base="{{ secure_url('/admin/products') }}"><label>Product name<input required name="title" maxlength="120" placeholder="e.g. Everyday tote bag"></label><div class="form-row"><label>Price (USD)<input required type="number" name="price" min="0" step="0.01" placeholder="0.00"></label><label>Stock<input required type="number" name="stock" min="0" step="1" placeholder="0"></label></div><div class="form-row"><label>Category<select required name="category">@foreach($categories as $item)<option value="{{ $item['slug'] ?? $item }}">{{ $item['name'] ?? ucfirst(str_replace('-', ' ', $item)) }}</option>@endforeach</select></label><label>Brand<input name="brand" maxlength="100" placeholder="Brand name"></label></div><label>Image URL<input type="url" name="thumbnail" placeholder="https://example.com/image.jpg"></label><label>Description<textarea name="description" rows="3" placeholder="Describe this product..."></textarea></label><p class="dialog-note">DummyJSON accepts demo changes, but does not permanently save them.</p><div class="dialog-actions"><button class="button button-plain" type="button" data-close-dialog>Cancel</button><button class="button button-primary" type="submit">Save product</button></div></form></dialog>
<div class="toast" role="status" data-toast></div>
@endsection

