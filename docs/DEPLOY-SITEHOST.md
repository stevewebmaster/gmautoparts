# Deploy G&M Autospares to SiteHost

Use this after you’ve pushed the latest code to GitHub (`git push origin main`).

---

## New container – use only this directory

When you create a **new** SiteHost container, put the Laravel app in **one place only**:

**The only correct directory for the app:** **`~/container/application`**

- Clone or upload the project **into** `~/container/application` (so `artisan`, `app/`, `public/`, `.env` all live there).
- Do **not** clone into a different folder (e.g. not `~/gmautoparts`, not `~/gm`, not `~/application`). If you clone into the wrong place, you get two copies and the site will use only one.
- Nginx is preconfigured to use **`/container/application/public`** as the web root. That path is **`~/container/application/public`** in SSH. So the app **must** be at `~/container/application`.

**Checklist for a new container:**

1. Create the container in SiteHost.
2. SSH in. Go to `~/container`.
3. Clone **directly into** `application`:  
   `git clone https://github.com/stevewebmaster/gmautoparts.git application`  
   That creates **`~/container/application`** with the app inside. You now have **one** app directory.
4. Do all later steps (composer, .env, migrate, etc.) from **`cd ~/container/application`**. Never create or use a second copy elsewhere.

---

## ⚠️ One app directory only (SiteHost)

**Use exactly one directory for the Laravel app on the server.**

| Purpose | Directory (SSH) | Do this |
|--------|------------------|---------|
| **The only app** | **`~/container/application`** | Clone or upload the project **here only**. Run all `git pull`, `composer`, `php artisan`, and `.env` edits **in this directory**. |
| **Nginx document root** | `~/container/application/public` | Nginx must point to this (as `/container/application/public` inside the container). |

**Do not** create a second copy of the app elsewhere (e.g. `~/gmautoparts` or any other folder). Having two copies causes confusion: the site only runs from the directory Nginx uses. If you deploy or edit the other copy, the live site won’t see your changes and things like the mini-app PIN will appear to “not work.” Always use **`~/container/application`** only.

---

## SiteHost Cloud Containers – paths (important)

**Two different views of the filesystem:**

| View | Path to Laravel app | Path to public (web root) |
|------|----------------------|----------------------------|
| **SSH / FileZilla** (your login) | `~/container/application` or `/home/USER/container/application` | `~/container/application/public` |
| **Inside the container** (Nginx, PHP) | `/container/application` | **`/container/application/public`** |

**Exact path inside the container:** The document root Nginx must use is **`/container/application/public`** (no `/home/`, no username). That is the path as seen by the container itself.

**Paths under `/home/` are not accessible from the container.** They are only visible to SSH users (e.g. when you log in as webmnzgmauto3 you see `/home/webmnzgmauto3/...`). Nginx and PHP run **inside** the container and do **not** see `/home/...`. If you put a path like `/home/webmnzgmauto3/container/application/public` in Nginx config (e.g. in `~/container/config/nginx/sites-available/default` or `sites-enabled/default`), the site will fail (e.g. “File not found”) because the container cannot access that path. **Always use `/container/application/public`** in Nginx.

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

**Put the app in the correct directory only – `~/container/application`:**

```bash
cd ~/container
# If application already exists (e.g. leftover from an old deploy), remove it so we have only one copy:
rm -rf application
# Clone INTO "application" so the app lives at ~/container/application (this is the only directory we use):
git clone https://github.com/stevewebmaster/gmautoparts.git application
cd ~/container/application
```

**Important:** The clone target is **`application`** so that the app ends up at **`~/container/application`**. Do not clone into a different folder name (e.g. `gmautoparts`); that would create a second app path and cause the “wrong directory” / invalid PIN issues.

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
- `MINIAPP_PIN=1234` (or your chosen PIN) if you use the mini-app at `/app`

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
- **root** must be **`/container/application/public`** – the path as seen **inside the container**.  
  **Wrong:** `root /home/webmnzgmauto3/container/application/public;` (paths under `/home/` are not accessible from inside the container; only SSH users see them).  
  **Right:** `root /container/application/public;`

**Edit:**

```bash
nano ~/container/config/nginx/sites-available/default
```

Use a single `server { }` block with at least:

- `root /container/application/public;`
- `server_name localhost gm.websitemaster.co.nz;` (or your domain)
- **`location /` must use Laravel’s `try_files`**, not `=404` (see warning below).
- `location ~ \.php$` with `fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;` and `fastcgi_pass unix:/var/run/php5-fpm.sock;`

**⚠️ Default SiteHost template is wrong for Laravel:** it often has  
`try_files $uri $uri/ =404;`  
That serves `/` via `index.php` sometimes, but **`/admin`, `/parts`, etc. return nginx 404** because the request never reaches `index.php`. Replace with:

`try_files $uri $uri/ /index.php?$query_string;`

**Example `server { }` block (adjust `server_name` and socket if needed):**

```nginx
server {
	listen 80 default_server;
	root /container/application/public;
	index index.php index.html;
	server_name localhost gm.websitemaster.co.nz;

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		fastcgi_param HTTP_PROXY "";
		fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
	}
}
```

After saving, **restart the container** in the SiteHost panel so Nginx reloads the config.

---

## 4. HTTPS and mixed content

- Set **`APP_URL=https://yourdomain.co.nz`** in `.env` (no trailing slash).
- The app’s **AppServiceProvider** forces `URL::forceScheme('https')` when `APP_URL` starts with `https://`, so asset and form URLs are generated over HTTPS behind the proxy. That avoids “Mixed Content” when the site is opened via HTTPS.
- The app includes **`TrustProxies`** middleware so Laravel respects `X-Forwarded-Proto` / `X-Forwarded-For` from SiteHost’s reverse proxy. Without that, redirects and cookies can behave as if the request were HTTP.
- After changing `.env` or deploying code:  
  `php artisan config:clear` then `php artisan config:cache`.

**If HTTPS still fails after enabling SSL in the SiteHost panel:**

1. In SiteHost, confirm the certificate is **issued/active** for the **exact hostname** you use (e.g. `www` vs non-`www` may need both or a redirect rule).
2. Wait a few minutes after enabling SSL; retry in a private/incognito window.
3. From your Mac: `curl -sI https://yourdomain.co.nz` — check for `HTTP/2 200` or `301` to HTTPS, not connection errors.
4. **Restart the Cloud Container** in the panel after SSL or domain changes.

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

**If `git pull` fails with** `Your local changes ... storage/logs/laravel.log would be overwritten`:

```bash
cd ~/container/application
git restore storage/logs/laravel.log
git pull origin main
```

`storage/logs/laravel.log` is a runtime log file on the server and should not block deploys.

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
- **“File not found” / Nginx realpath() failed:** Nginx is using the wrong root. It must be **`/container/application/public`** (the path as seen inside the container). Do **not** use any path under `/home/` (e.g. `/home/webmnzgmauto3/container/application/public`) – the container cannot access `/home/`. Edit `~/container/config/nginx/sites-available/default` (and `sites-enabled/default` if you edit that), set `root /container/application/public;`, then restart the container.
- **`/admin` or other routes 404 with small nginx page, but `/` works:** Your `location /` likely has **`try_files $uri $uri/ =404;`**. Change it to **`try_files $uri $uri/ /index.php?$query_string;`** and add **`fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;`** inside the `\.php$` block, then restart the container.
- **Admin login works but dashboard gives 500 (`Route [filament.admin.resources...index] not defined`):** Server is on old code or missing Filament resource page classes. Verify server commit (`git rev-parse --short HEAD`) matches GitHub `main`, run `composer dump-autoload -o`, `php artisan optimize:clear`, and check `php artisan route:list --path=admin` includes resource routes (e.g. `admin/pages`, `admin/parts`, `admin/vehicles`), not just login/logout.
- **Admin login returns 403 after password accepted:** Ensure `app/Models/User.php` implements `FilamentUser` and has `canAccessPanel(Panel $panel): bool { return true; }`, then deploy that commit, clear caches, and restart the container to flush opcache.
- **Password reset in Tinker still fails login:** Save a hashed password, e.g. `$user->password = bcrypt('NewPassword123!'); $user->save();` (plain text in DB will not authenticate).
- **Duplicate default server / Nginx won’t start:** Remove or empty `~/container/config/nginx/conf.d/default.conf` so only `sites-available/default` defines the default server.
- **Mixed content on HTTPS (CSS/JS blocked):** Set `APP_URL=https://yourdomain.co.nz` and ensure the AppServiceProvider change that forces HTTPS when APP_URL is https is deployed; then `php artisan config:cache`.
- **DB connection:** Use `DB_HOST=mariadb1011` (or the host SiteHost gives), not localhost.
- **Admin user:** Use `php artisan gm:create-admin`, not `make:filament-user`.
- **Laravel errors:** Check `~/container/application/storage/logs/laravel.log`. Ensure `storage` and `bootstrap/cache` are writable (e.g. `chmod -R 775 storage bootstrap/cache`).
- **Mini-app “Invalid PIN” or 404:** The site runs only from `~/container/application`. If you have another copy of the app (e.g. `~/gmautoparts`), edits and `.env` there are ignored. Set `MINIAPP_PIN` in **`~/container/application/.env`** and run `php artisan config:clear` and `php artisan config:cache` from **`~/container/application`** only.

---

## Reference – where everything is on the server

| What | Location (SSH / FileZilla) | Location (inside container) |
|------|---------------------------|-----------------------------|
| **Laravel app (only copy)** | **`~/container/application`** | `/container/application` |
| Web root (document root) | `~/container/application/public` | `/container/application/public` |
| Nginx config | `~/container/config/nginx/sites-available/default` | — |
| Nginx error log | `~/container/logs/nginx/error.log` | — |
| Laravel log | `~/container/application/storage/logs/laravel.log` | — |
| .env | `~/container/application/.env` | — |

**Do not** use `/home/...` in Nginx config; the container cannot see those paths. **Do not** put the app in any other directory (e.g. `~/gmautoparts`); use **`~/container/application`** only.
