# Everyday Goods · Product Catalog Admin

A Laravel 12 admin-style catalog for browsing products from [DummyJSON](https://dummyjson.com/docs/products). It includes product search, category filters, inventory status, ratings, product details, and add/edit product dialogs.

## Requirements

- PHP 8.2 or newer with the cURL, Mbstring, OpenSSL, PDO, and Tokenizer extensions
- Composer 2
- A connection to `dummyjson.com`

## Run locally

1. Install the PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`.
3. Create an application key with `php artisan key:generate`.
4. Start the local server with `php artisan serve`.
5. Open `http://127.0.0.1:8000`.

The interface is built with Blade and plain CSS/JavaScript, so no Node build step is needed.

## Vercel configuration

The Vercel deployment files use Vercel's container runtime with FrankenPHP. Deploy with Docker available and set `APP_KEY` to the output of `php artisan key:generate --show`. Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` to the deployed domain. The filesystem and in-memory cache are temporary; use managed database/cache services for durable data.

## Data behavior

DummyJSON is a demonstration API. Search and reads use the live API. Its add and update endpoints return simulated results and do not permanently change the upstream catalog. The runtime uses in-memory caching and temporary storage, so this project does not provide durable administration or authentication. Add a persistent database and admin sign-in before using it for real store operations.
