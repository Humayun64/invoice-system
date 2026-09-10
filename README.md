# SKM Engineering – Laravel System Setup Guide

## Requirements
- PHP 8.2+
- Composer
- MySQL 5.7+ / MariaDB
- Laravel 12

---

## Step 1 — Install Laravel

Open terminal/command prompt and run:

```bash
composer create-project laravel/laravel skm_laravel
cd skm_laravel
```

---

## Step 2 — Copy These Files

Copy all files from this zip into your `skm_laravel` folder, **replacing** existing files:

```
skm_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        ← COPY
│   │   │   ├── DashboardController.php   ← COPY
│   │   │   ├── InvoiceController.php     ← COPY
│   │   │   ├── QuotationController.php   ← COPY
│   │   │   ├── ClientController.php      ← COPY
│   │   │   └── SettingsController.php    ← COPY
│   │   └── Middleware/
│   │       └── AuthMiddleware.php        ← COPY
│   └── Models/
│       ├── User.php          ← COPY
│       ├── Company.php       ← COPY
│       ├── Client.php        ← COPY
│       ├── Invoice.php       ← COPY
│       ├── InvoiceItem.php   ← COPY
│       ├── Quotation.php     ← COPY
│       └── QuotationItem.php ← COPY
├── bootstrap/
│   └── app.php               ← COPY (replace existing)
├── resources/views/
│   ├── auth/login.blade.php        ← COPY
│   ├── layouts/app.blade.php       ← COPY
│   ├── dashboard.blade.php         ← COPY
│   ├── invoices/
│   │   ├── index.blade.php   ← COPY
│   │   ├── create.blade.php  ← COPY
│   │   └── print.blade.php   ← COPY
│   ├── quotations/
│   │   ├── index.blade.php   ← COPY
│   │   ├── create.blade.php  ← COPY
│   │   └── print.blade.php   ← COPY
│   ├── clients/
│   │   └── index.blade.php   ← COPY
│   └── settings/
│       └── index.blade.php   ← COPY
├── routes/
│   └── web.php               ← COPY (replace existing)
└── database/
    └── skm_laravel.sql       ← IMPORT to MySQL
```

---

## Step 3 — Configure .env

Open `.env` file and update:

```env
APP_NAME="SKM Engineering"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skm_laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 4 — Import Database

Open phpMyAdmin → SQL tab → paste contents of `database/skm_laravel.sql` → Go

---

## Step 5 — Generate App Key

```bash
php artisan key:generate
```

---

## Step 6 — Create Assets Folder

```bash
mkdir public/assets
```

Copy your `logo.png` to `public/assets/logo.png`

---

## Step 7 — Run the App

```bash
php artisan serve
```

Open: http://localhost:8000

---

## Login
- Username: `admin`
- Password: `admin123`

**Note:** If login fails, run this in terminal:
```bash
php artisan tinker
>>> \App\Models\User::first()->update(['password' => bcrypt('admin123')]);
```

---

## Live Server Deployment

### Using Shared Hosting (cPanel):
1. Upload all files to `public_html/skm_laravel/`
2. Point document root to `public_html/skm_laravel/public/`
3. Import `skm_laravel.sql` via cPanel phpMyAdmin
4. Update `.env` with live DB credentials
5. Run: `php artisan key:generate`
6. Run: `php artisan config:cache`

### Using VPS:
```bash
git clone or upload files
composer install --no-dev
php artisan key:generate
php artisan config:cache
php artisan route:cache
```

---

## Features
- ✅ Admin login / logout
- ✅ Dashboard with stats
- ✅ Invoices — create, edit, delete, print, PDF
- ✅ Quotations — create, edit, delete, print, PDF
- ✅ Convert Quotation → Invoice (one click)
- ✅ QTY / UNIT / UNIT PRICE toggle
- ✅ Deposit option (1st/2nd/3rd/4th/5th/Final)
- ✅ Rich text editor (bold, italic, bullet, numbered)
- ✅ Search & filter
- ✅ Client management
- ✅ Company settings
- ✅ Logo upload
- ✅ Change password
- ✅ Multi-page PDF download
