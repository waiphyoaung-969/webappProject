@extends('layouts.admin')
@section('title', $product['title'])
@section('section', 'Product details')
@section('content')
<a class="back-link" href="{{ route('products.index') }}">← Back to products</a>
<section class="detail-card"><div class="detail-image"><img src="{{ $product['thumbnail'] }}" alt="{{ $product['title'] }}"></div><div class="detail-copy"><div class="eyebrow">{{ strtoupper(str_replace('-', ' ', $product['category'])) }}</div><h1>{{ $product['title'] }}</h1><p class="detail-brand">{{ $product['brand'] ?? 'Everyday Goods' }}</p><div class="detail-rating"><span>★</span> {{ number_format($product['rating'], 1) }} <span class="muted">· {{ $product['stock'] }} in stock</span></div><div class="detail-price">${{ number_format($product['price'], 2) }} <span>{{ number_format($product['discountPercentage'], 0) }}% off</span></div><p class="detail-description">{{ $product['description'] }}</p><span class="pill {{ $product['stock'] > 0 ? 'pill-green' : 'pill-red' }}"><i></i>{{ $product['stock'] > 0 ? 'In stock' : 'Out of stock' }}</span><div class="detail-actions"><a class="button button-primary" href="{{ route('products.index') }}">Manage products</a><a class="button button-plain" href="{{ route('products.index') }}">Back to catalog</a></div></div></section>
@if(count($product['images'] ?? []) > 1)<section class="gallery"><h2>Product images</h2><div>@foreach($product['images'] as $image)<img src="{{ $image }}" alt="{{ $product['title'] }}" loading="lazy">@endforeach</div></section>@endif
@endsection
