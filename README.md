# Mini CRM

Laravel 12 mini-CRM for collecting and processing support tickets via an embeddable widget, REST API, and admin panel.

## Stack

- PHP 8.4, Laravel 12
- MySQL 8.0
- spatie/laravel-permission (roles)
- spatie/laravel-medialibrary (file attachments)

## How to Run

### Option 1: Docker

```bash
git clone https://github.com/kyan9400/test-test.git
cd test-test
cp .env.example .env
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker-compose exec app php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

Open in browser: **http://localhost:8080**

### Option 2: Local (without Docker)

**Prerequisites:** PHP 8.4, Composer, MySQL

1. Clone and install dependencies:

```bash
git clone https://github.com/kyan9400/test-test.git
cd test-test
composer install
```

2. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Create a MySQL database and update `.env` with your credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_crm
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. Publish packages and run migrations:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate --seed
php artisan storage:link
```

5. Start the server:

```bash
php artisan serve
```

Open in browser: **http://localhost:8000**

## Login Credentials

| Role    | Email                | Password |
|---------|----------------------|----------|
| Admin   | admin@example.com    | password |
| Manager | manager1@example.com | password |

## Widget

Embed via iframe:

```html
<iframe src="https://your-domain.com/widget" width="100%" height="600" frameborder="0"></iframe>
```

Or open directly: `GET /widget`

## API

### POST /api/tickets

Create a ticket (public, no auth).

| Field   | Type   | Required | Notes                        |
|---------|--------|----------|------------------------------|
| name    | string | yes      |                              |
| email   | string | yes      |                              |
| phone   | string | yes      | E.164 format (+1234567890)   |
| subject | string | yes      |                              |
| text    | string | yes      |                              |
| files[] | file[] | no       | max 5 files, 10MB each       |

Limit: 1 ticket per email/phone per day (returns 429).

### GET /api/tickets/statistics?period=day

Requires auth (Sanctum) + role admin|manager.

`period`: day / week / month

## Admin Panel

`GET /admin/tickets` — list with filters (status, date, email, phone)

`GET /admin/tickets/{id}` — ticket details + file downloads

`PATCH /admin/tickets/{id}/status` — change status

## Tests

```bash
php artisan test
```
