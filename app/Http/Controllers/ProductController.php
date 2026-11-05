<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProductController
{
    private const API = 'https://dummyjson.com';

    public function storefront(Request $request)
    {
        $catalog = $this->catalog($request);
        return view('store.index', $catalog);
    }

    public function adminIndex(Request $request)
    {
        $catalog = $this->catalog($request);
        $productCount = count($catalog['products']);
        return view('products.index', $catalog + compact('productCount'));
    }

    public function show(int $id)
    {
        try {
            $product = Cache::remember("catalog:product:{$id}", 300, fn () => Http::acceptJson()->timeout(8)->get(self::API.'/products/'.$id)->throw()->json());
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException $exception) {
            abort(503, 'The product service is temporarily unavailable.');
        }

        abort_unless(isset($product['id']), 404);
        return view('store.show', compact('product'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'url', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        return $this->mutate('POST', '/products/add', $data);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'url', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        return $this->mutate('PUT', '/products/'.$id, $data, $id);
    }

    public function destroy(int $id)
    {
        try {
            $product = Http::acceptJson()->timeout(8)->delete(self::API.'/products/'.$id)->throw()->json();
            Cache::forget("catalog:product:{$id}");
            return response()->json(['success' => true, 'product' => $product]);
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException $exception) {
            return response()->json(['message' => 'The product service is unavailable. Please try again.'], 502);
        }
    }

    private function catalog(Request $request): array
    {
        $category = trim((string) $request->query('category', ''));
        $search = trim((string) $request->query('q', ''));
        $url = $search !== '' ? '/products/search' : ($category !== '' ? '/products/category/'.rawurlencode($category) : '/products');
        $query = ['limit' => 100, 'select' => 'id,title,description,category,price,discountPercentage,rating,stock,brand,thumbnail,images'];
        if ($search !== '') $query['q'] = $search;

        try {
            $products = Cache::remember('catalog:'.md5($url.http_build_query($query)), 300, function () use ($url, $query) {
                $response = Http::acceptJson()->timeout(8)->retry(2, 150)->get(self::API.$url, $query)->throw()->json();
                return $response['products'] ?? [];
            });
            $categories = Cache::remember('catalog:categories', 3600, fn () => Http::acceptJson()->timeout(8)->get(self::API.'/products/category-list')->throw()->json());
            $apiError = false;
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException $exception) {
            $products = [];
            $categories = [];
            $apiError = true;
        }

        return compact('products', 'categories', 'category', 'search', 'apiError');
    }

    private function mutate(string $method, string $path, array $data, ?int $id = null)
    {
        try {
            $product = Http::acceptJson()->timeout(8)->send($method, self::API.$path, ['json' => $data])->throw()->json();
            if ($id !== null) Cache::forget("catalog:product:{$id}");
            return response()->json(['success' => true, 'product' => $product]);
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException $exception) {
            return response()->json(['message' => 'The product service is unavailable. Please try again.'], 502);
        }
    }
}

