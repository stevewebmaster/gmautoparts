# Deploy G&M Autospares to SiteHost

Use this after you’ve pushed the latest code to GitHub (`git push origin main`).

---

## SiteHost Cloud Containers – paths (important)

**Two different views of the filesystem:**

| View | Path to Laravel app | Path to public (web root) |
|------|----------------------|----------------------------|
| **SSH / FileZilla** (your login) | `~/container/application` or `/home/USER/container/application` | `~/container/application/public` |
| **Inside the container** (Nginx, PHP) | `/container/application` | `/container/application/public` |

- Paths under **`/home/`** are only visible to SSH users. **Nginx and PHP run inside the container** and do **not** see `/home/...`. Never use `/home/...` in Nginx config.
- The **document root** Nginx must use is **`/container/application/public`** (the path as seen inside the container).
- Put the Laravel app so it lives at **`~/container/application`** (clone or copy there). The container then exposes it as `/container/application`.

**Stack used:** Nginx + PHP 8.4 (e.g. 1.0.4-noble). Database host: **mariadb1011** (not localhost) – set in `.env` as `DB_HOST=mariadb1011`.

---

## 1. On your Mac – push latest to GitHub

```bash
cd /Users/stevepeters/Files/GM
git status
git add .
git commit -m "Your message"
git push origin main
```

---

## 2. On SiteHost – first-time setup (app in container)

**SSH in** with the user that has access to the container (e.g. webmnzgmauto3).

**Put the app in the container’s application folder:**

```bash
cd ~/container
# If application already exists and you want a fresh deploy:
rm -rf application
git clone https://github.com/stevewebmaster/gmautoparts.git application
cd ~/container/application
```

**Install and configure:**

```bash
composer install --no-dev --optimize-autoloader
cp .env.sitehost.example .env
php artisan key:generate
nano .env
```

In `.env` set at least:

- `APP_URL=https://gm.websitemaster.co.nz` (or your domain; **https**, no trailing slash)
- `APP_DEBUG=false`
- `DB_HOST=mariadb1011`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (SiteHost MySQL)
- `MAIL_*` if you use contact form

Then:

```bash
php artisan migrate --force
php artisan db:seed
php artisan storage:link
php artisan gm:create-admin
chmod -R 775 storage bootstrap/cache
```

**Note:** Use **`php artisan gm:create-admin`** to create the admin user, not `php artisan make:filament-user` (Filament’s install check can block that command).

---

## 3. Nginx config (document root and Laravel)

The file that matters is **`~/container/config/nginx/sites-available/default`** (linked from `sites-enabled`).

- **Do not** add a second default server in `conf.d/default.conf` – that causes “duplicate default server” and Nginx will not start.
- **root** must be **`/container/application/public`** (path as seen inside the container).

**Edit:**

```bash
nano ~/container/config/nginx/sites-available/default
```

Use a single `server { }` block with at least:

- `root /container/application/public;`
- `server_name localhost gm.websitemaster.co.nz;` (or your domain)
- `location / { try_files $uri $uri/ /index.php?$query_string; }`
- `location ~ \.php$` with `fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;` and `fastcgi_pass unix:/var/run/php5-fpm.sock;`

After saving, **restart the container** in the SiteHost panel so Nginx reloads the config.

---

## 4. HTTPS and mixed content

- Set **`APP_URL=https://yourdomain.co.nz`** in `.env` (no trailing slash).
- The app’s **AppServiceProvider** forces `URL::forceScheme('https')` when `APP_URL` starts with `https://`, so asset and form URLs are generated over HTTPS behind the proxy. That avoids “Mixed Content” when the site is opened via HTTPS.
- After changing `.env` or deploying code:  
  `php artisan config:clear` then `php artisan config:cache`.

---

## 5. Quick “update only” (after first deploy)

```bash
cd ~/container/application
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

---

## When PHP/Composer aren’t in SSH (build on Mac, upload)

On some SiteHost SSH users, `php` and `composer` are not in PATH. Then:

1. On your Mac: `composer install --no-dev --optimize-autoloader`, then zip the project (excluding `.git`, `node_modules`, `.env`).
2. Upload the zip and extract into **`~/container/application`** (so the container sees it at `/container/application`).
3. On the server: keep your existing `.env`; ensure `APP_KEY` is set (e.g. generate on Mac with `php artisan key:generate --show` and paste into server `.env`).
4. Storage link: `cd ~/container/application/public && ln -sf ../storage/app/public storage` (or `php artisan storage:link` if PHP is available).
5. Nginx root must still be **`/container/application/public`**.

---

## Troubleshooting

- **500 “No application encryption key”:** Run `php artisan key:generate` in `~/container/application` and ensure `.env` has `APP_KEY=base64:...`.
- **“File not found” / Nginx realpath() failed:** Nginx is using the wrong root. It must be **`/container/application/public`** (not `/application/public`, not `/container/public`, not any `/home/...` path). Edit `~/container/config/nginx/sites-available/default` and restart the container.
- **Duplicate default server / Nginx won’t start:** Remove or empty `~/container/config/nginx/conf.d/default.conf` so only `sites-available/default` defines the default server.
- **Mixed content on HTTPS (CSS/JS blocked):** Set `APP_URL=https://yourdomain.co.nz` and ensure the AppServiceProvider change that forces HTTPS when APP_URL is https is deployed; then `php artisan config:cache`.
- **DB connection:** Use `DB_HOST=mariadb1011` (or the host SiteHost gives), not localhost.
- **Admin user:** Use `php artisan gm:create-admin`, not `make:filament-user`.
- **Laravel errors:** Check `~/container/application/storage/logs/laravel.log`. Ensure `storage` and `bootstrap/cache` are writable (e.g. `chmod -R 775 storage bootstrap/cache`).

---

## Reference – where everything is on the server

| What | Location (SSH / FileZilla) | Location (inside container) |
|------|---------------------------|-----------------------------|
| Laravel app root | `~/container/application` | `/container/application` |
| Web root (document root) | `~/container/application/public` | `/container/application/public` |
| Nginx config | `~/container/config/nginx/sites-available/default` | — |
| Nginx error log | `~/container/logs/nginx/error.log` | — |
| Laravel log | `~/container/application/storage/logs/laravel.log` | — |
| .env | `~/container/application/.env` | — |

**Do not** use `/home/...` in Nginx config; the container cannot see those paths.
