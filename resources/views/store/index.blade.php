@extends('layouts.store')
@section('title', 'Shop')
@section('content')
<section class="store-hero"><div class="store-kicker">A LITTLE EVERYDAY MAGIC</div><h1>Find your next<br><span>favorite thing.</span></h1><p>Thoughtfully picked essentials for the way you live.</p><a class="store-hero-link" href="#shop">Explore the collection <span>↓</span></a><div class="hero-orb hero-orb-one"></div><div class="hero-orb hero-orb-two"></div></section>
@if($apiError)<div class="store-error"><strong>We couldn’t load the catalog right now.</strong> Please refresh the page in a moment.</div>@endif
<section class="shop-section" id="shop">
    <div class="shop-heading"><div><div class="store-kicker">THE COLLECTION</div><h2>Shop all products</h2><p>{{ count($products) }} items picked for you</p></div><form class="store-filters" action="{{ route('store.index') }}" method="GET"><label class="store-search"><span>⌕</span><input type="search" name="q" value="{{ $search }}" placeholder="Search the collection" aria-label="Search products"></label><select name="category" aria-label="Filter by category" onchange="this.form.submit()"><option value="">All categories</option>@foreach($categories as $item)<option value="{{ $item['slug'] ?? $item }}" @selected($category === ($item['slug'] ?? $item))>{{ $item['name'] ?? ucfirst(str_replace('-', ' ', $item)) }}</option>@endforeach</select>@if($search || $category)<a class="store-clear" href="{{ route('store.index') }}">Clear</a>@endif</form></div>
    <div class="product-grid">
    @forelse($products as $product)
        <a class="shop-card" href="{{ route('store.products.show', $product['id']) }}"><div class="shop-image"><img src="{{ $product['thumbnail'] ?? '' }}" alt="{{ $product['title'] }}" loading="lazy" onerror="this.src='https://placehold.co/600x500/e4f7ff/4695b5?text=Everyday+Goods'"><span class="shop-category">{{ ucfirst(str_replace('-', ' ', $product['category'])) }}</span></div><div class="shop-product-info"><div><h3>{{ $product['title'] }}</h3><p>{{ $product['brand'] ?? 'Everyday Goods' }}</p></div><div class="shop-product-meta"><strong>${{ number_format($product['price'], 2) }}</strong><span class="shop-rating">★ {{ number_format($product['rating'], 1) }}</span></div></div></a>
    @empty
        <div class="shop-empty"><span>⌕</span><h3>No products found</h3><p>Try another search or category.</p><a href="{{ route('store.index') }}">See all products</a></div>
    @endforelse
    </div>
</section>
@endsection

