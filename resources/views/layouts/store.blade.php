<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop') · Everyday Goods</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/store.css', true) }}?v=1" rel="stylesheet">
</head>
<body>
<header class="store-header"><a class="store-brand" href="{{ route('store.index') }}"><span class="store-mark">e</span><span>everyday<span>goods</span></span></a><nav class="store-nav" aria-label="Main navigation"><a class="store-nav-active" href="{{ route('store.index') }}">Shop</a><a href="{{ route('admin.products.index') }}">Admin</a></nav></header>
<main class="store-main">@yield('content')</main>
<footer class="store-footer"><span>Everyday Goods</span><span>Product data powered by <a href="https://dummyjson.com" target="_blank" rel="noreferrer">DummyJSON</a></span></footer>
</body>
</html>

