# Shopify Backend
Manages Shopify product/category syncing.

Built with Laravel 13.31.0

## Prerequisites
- PHP 8.3 to 8.5
- MariaDB server 10.4 to 11.4
- Composer 2.8
- Node.js 24.5

---

## Setup
After cloning this repository, run following command in terminal:
~~~
composer update
~~~

Copy **.env.example** and rename to **.env**.
Modify where appropriate e.g. **APP_URL**, **MAIL_HOST**, **DB_HOST** etc.


Then run following after creating your database:
~~~
php artisan migrate
~~~

~~~
npm install
~~~

If on development server:
~~~
npm run dev
~~~

Or if on production server:
~~~
npm run build
~~~

---

## Shopify Setup
This app talks to the Shopify Admin API, so you'll need a store to connect it to:

1. In your Shopify admin, go to **Settings > Apps and sales channels > Develop apps** and create a custom app.
2. Under **Configuration**, grant the Admin API scopes `read_products` and `write_products`.
3. Install the app on your store and copy the generated **Admin API access token**.
4. Provide the credentials either via `.env`:
   ~~~
   SHOPIFY_STORE=your-store-name
   SHOPIFY_ACCESS_TOKEN=shpat_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
   SHOPIFY_API_VERSION=2024-10
   ~~~
   or later via the app's **Settings** page, which takes precedence over `.env` values.

---
