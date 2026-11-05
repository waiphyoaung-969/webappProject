@extends('layouts.store')
@section('title', $product['title'])
@section('content')
<a class="store-back" href="{{ route('store.index') }}">← Back to shop</a>
<section class="store-detail"><div class="store-detail-image"><img src="{{ $product['thumbnail'] ?? '' }}" alt="{{ $product['title'] }}"></div><div class="store-detail-copy"><div class="store-kicker">{{ strtoupper(str_replace('-', ' ', $product['category'])) }}</div><h1>{{ $product['title'] }}</h1><p class="store-detail-brand">{{ $product['brand'] ?? 'Everyday Goods' }}</p><div class="store-detail-rating"><span>★ {{ number_format($product['rating'], 1) }}</span><span>{{ $product['stock'] }} in stock</span></div><div class="store-detail-price">${{ number_format($product['price'], 2) }} @if(($product['discountPercentage'] ?? 0) > 0)<span>{{ number_format($product['discountPercentage'], 0) }}% off</span>@endif</div><p class="store-detail-description">{{ $product['description'] }}</p><span class="store-stock {{ ($product['stock'] ?? 0) > 0 ? '' : 'store-out' }}">{{ ($product['stock'] ?? 0) > 0 ? 'In stock and ready to ship' : 'Currently out of stock' }}</span><a class="store-buy-link" href="{{ route('store.index') }}">Continue shopping <span>→</span></a></div></section>
@if(count($product['images'] ?? []) > 1)<section class="store-gallery"><h2>More views</h2><div>@foreach($product['images'] as $image)<img src="{{ $image }}" alt="{{ $product['title'] }}" loading="lazy">@endforeach</div></section>@endif
@endsection

