# Appsolutely

## Features

### CMS management

### Product management

### Order management

### Payment Gateway

### Api ready

### Well-organised structure

## Installation

General Laravel Installation
```bash
composer install

php artisan migrate && php artisan db:seed

php artisan db:seed --class=AdminCoreSeeder
```

Install hooks for local environment.
```
composer install-hooks
```

## Todo
- Order management
- Front-end: public site (homepage, articles, products and categories, checkout) and member center
- Payment management
- Batch deletion for product SKUs
- Create articles and products/product skus before inserting images
- Guest checkout
