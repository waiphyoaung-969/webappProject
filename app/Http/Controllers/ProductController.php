<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProductController
{
    private const API = 'https://dummyjson.com';

    public function index(Request $request)
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
        } catch (ConnectionException|Illuminate\Http\Client\RequestException $exception) {
            $products = [];
            $categories = [];
            $apiError = true;
        }

        return view('products.index', compact('products', 'categories', 'category', 'search', 'apiError'));
    }

    public function show(int $id)
    {
        try {
            $product = Cache::remember("catalog:product:{$id}", 300, fn () => Http::acceptJson()->timeout(8)->get(self::API.'/products/'.$id)->throw()->json());
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException $exception) {
            abort(503, 'The product service is temporarily unavailable.');
        }
        abort_unless(isset($product['id']), 404);
        return view('products.show', compact('product'));
    }
}
