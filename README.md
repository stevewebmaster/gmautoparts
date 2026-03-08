# G&M Autospares – Laravel Website

Laravel 10 website for G&M Autospares (automotive wrecker, New Zealand) with parts catalogue, filtering, “Now Dismantling” vehicles, and Filament admin panel.

## Requirements

- PHP 8.2+
- Composer
- MySQL 5.7+ (or MariaDB)
- Nginx (or Apache) with PHP 8.4

## Installation (e.g. on SiteHost)

1. **Clone or upload** the project to your server (e.g. `public_html` or your site root). Ensure the web root points to the `public` folder (e.g. Nginx `root /path/to/GM/public;`).

2. **Install dependencies**
   ```bash
   cd /path/to/GM
   composer install --no-dev --optimize-autoloader
   ```

3. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` and set:
   - `APP_URL` – your site URL (e.g. `https://gmautospares.co.nz`)
   - `APP_DEBUG=false` in production
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` – your MySQL credentials
   - `MAIL_*` – SMTP settings for contact form and part enquiries
   - `ADMIN_EMAIL` – address to receive contact/enquiry emails (optional; can use `MAIL_FROM_ADDRESS`)

4. **Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Storage link** (for uploads and images)
   ```bash
   php artisan storage:link
   ```
   This creates `public/storage` → `storage/app/public`. Parts, vehicles, and slider images are stored under `storage/app/public/`.

6. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```
   Enter name, email, and password for the first admin.

7. **Permissions** (if needed)
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache   # Linux; use your web server user
   ```

## Nginx example

```nginx
server {
    listen 80;
    server_name gmautospares.co.nz;
    root /path/to/GM/public;

    add_header X-Frame-Options "SAMEORIGIN";
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

## After deployment

- **Admin panel:** `https://yoursite.co.nz/admin`
- **Client guide:** See [docs/CLIENT-GUIDE.md](docs/CLIENT-GUIDE.md) for adding parts, vehicles, slider slides, and editing pages.

## Tech stack

- Laravel 10
- Filament 3 (admin)
- Livewire 3 (parts filter without full page reload)
- MySQL
- Blade templates, Tailwind-style CSS (custom dark/grey/blue theme)

## Rate limiting

- Contact form: 5 submissions per minute per IP
- Part enquiry form: 10 per minute per IP

Configured in `routes/web.php` and can be adjusted if needed.
