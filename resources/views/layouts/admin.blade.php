<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catalog') · Everyday Goods Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css', true) }}?v=2" rel="stylesheet">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('products.index') }}"><span class="brand-mark">e</span><span>everyday<span class="brand-light">goods</span></span></a>
        <div class="workspace-label">WORKSPACE</div>
        <nav class="side-nav">
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}"><span class="nav-icon">▦</span> Products <span class="nav-count">{{ $productCount ?? '—' }}</span></a>
            <a href="{{ route('products.index') }}#categories" class="nav-item"><span class="nav-icon">◇</span> Categories</a>
            <a href="{{ route('products.index') }}#insights" class="nav-item"><span class="nav-icon">▥</span> Insights</a>
        </nav>
        <div class="sidebar-bottom"><div class="help-card"><div class="help-icon">?</div><strong>Need a hand?</strong><p>Your catalog data is provided by DummyJSON.</p><a href="https://dummyjson.com/docs/products" target="_blank" rel="noreferrer">View API docs <span>↗</span></a></div><div class="admin-profile"><div class="avatar">AD</div><div><strong>Catalog admin</strong><small>Administrator</small></div><span class="profile-dots">···</span></div></div>
    </aside>
    <main class="main-area">
        <header class="topbar"><div class="breadcrumb">Workspace <span>/</span> <strong>@yield('section', 'Products')</strong></div><div class="topbar-right"><span class="status-dot"></span><span class="sync-label">Connected to DummyJSON</span><button class="icon-button" aria-label="Notifications">♧</button><div class="avatar avatar-small">AD</div></div></header>
        <div class="page-content">@yield('content')</div>
        <footer class="footer">Catalog Admin <span>·</span> Data powered by <a href="https://dummyjson.com" target="_blank" rel="noreferrer">DummyJSON</a></footer>
    </main>
</div>
<script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>

