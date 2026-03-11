# Deploy G&M Autospares to SiteHost

Use this after you’ve pushed the latest code to GitHub (`git push origin main`).

---

## SiteHost Cloud Containers – folder structure

On **SiteHost Cloud Containers**:

- Put your **application files** in **`/application`**.
- The **web root** (what the browser hits) must be **`/application/public`** — i.e. Laravel’s `public` folder inside the app.

So: **project root = `/application`**, **web root = `/application/public`**.  
If the site is wrong (e.g. 404, or no CSS), the web root is usually set to the project root instead of `public`. Fix by setting Nginx `root` or Apache `DocumentRoot` (under `/config`) to **`/application/public`**.

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

## 2. On SiteHost container – first-time setup (or fresh clone)

**SSH into your SiteHost container.** The container often has no sudo and no SSH key for GitHub, so clone via **HTTPS** into your home directory:

```bash
cd ~
git clone https://github.com/stevewebmaster/gmautoparts.git gmautoparts
cd gmautoparts
```

Then set the site’s **web root** (in the panel or under `/config`) to **`/home/YOUR_USERNAME/gmautoparts/public`** (replace `YOUR_USERNAME` with your SSH user, e.g. `webmnzgmauto2`).

**If the repo is already in `~/gmautoparts`** (you’ve done this before), just pull:

```bash
cd ~/gmautoparts
git pull origin main
```

**Alternative:** If you have write access to `/application` (or can use sudo), you can put the app there and use web root **`/application/public`** instead.

---

## 3. On SiteHost container – install/update and configure

Run these in the project folder (e.g. **`~/gmautoparts`** or **`/application`** if you put the app there):

```bash
# Install PHP dependencies (first time or after composer.json change)
composer install --no-dev --optimize-autoloader

# First time only: copy env and generate key (use SiteHost template if you have it)
cp .env.sitehost.example .env   # or: cp .env.example .env
php artisan key:generate

# Edit .env with your SiteHost values (use nano, vim, or panel file editor)
# Set: APP_URL, APP_DEBUG=false, DB_*, MAIL_*, etc.
nano .env
```

Then:

```bash
# Database (first time: migrate + seed; updates: migrate only)
php artisan migrate --force
php artisan db:seed   # first time only

# Storage link for uploads (first time only)
php artisan storage:link

# Create admin user (first time only)
php artisan make:filament-user

# Permissions (use the user SiteHost runs PHP as, often www-data or your username)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www storage bootstrap/cache   # adjust www-data if different
```

---

## 4. Set web root to the Laravel `public` folder

The web root must be Laravel’s **`public`** folder (never the project root):

- If the app is in **`~/gmautoparts`**: set web root to **`/home/YOUR_USERNAME/gmautoparts/public`**
- If the app is in **`/application`**: set web root to **`/application/public`**

**Nginx** (under `/config`): `root /home/webmnzgmauto2/gmautoparts/public;`  
**Apache** (under `/config`): `DocumentRoot /home/webmnzgmauto2/gmautoparts/public`  
Or use the panel “Document root” / “Web root” and enter that path.

---

## 5. Quick “update only” (after first deploy)

When you’ve already set up the app and only need to deploy new code:

```bash
cd ~/gmautoparts   # or cd /application if you use /application
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

---

## When PHP/Composer aren’t in SSH (build on Mac, upload)

On some SiteHost containers, `php` and `composer` are not available in the SSH session. In that case:

1. **On your Mac** – build the app (install dependencies so `vendor/` exists):
   ```bash
   cd /Users/stevepeters/Files/GM
   composer install --no-dev --optimize-autoloader
   ```
2. **Zip the project** (excluding `.git`, `node_modules`, `.env`) and upload the zip via FileZilla to your home or `~/application` on the server. On the server (SSH): `cd ~/application && unzip -o ../gmautoparts.zip` (or unzip into a temp folder then copy into `~/application`). Do **not** overwrite the server’s `.env` – keep the one on the server with your DB settings.
3. **APP_KEY** – On the server’s `.env` you need an `APP_KEY=base64:...`. On your Mac run `php artisan key:generate --show` and paste that value into the server `.env`.
4. **Storage link (on server, no PHP needed):**
   ```bash
   cd ~/application/public
   ln -sf ../storage/app/public storage
   ```
5. **Migrations and admin user** – From your Mac, using the **remote** database: temporarily set `.env` (or a copy) with the SiteHost DB host (e.g. the hostname SiteHost gives for external DB access, if any), then run `php artisan migrate --force`, `php artisan db:seed`, `php artisan make:filament-user`. If the DB is only reachable from the container, ask SiteHost how to run these commands.
6. **Web root** – In the SiteHost panel, set document root to **`/home/webmnzgmauto2/application/public`** (or `~/application/public`).

---

## Troubleshooting

- **500 error:** Check `storage/logs/laravel.log`. Ensure `storage` and `bootstrap/cache` are writable (775 and owned by the web server user).
- **DB connection:** Confirm `.env` has the correct SiteHost database host, name, user, and password.
- **Admin not loading:** Ensure the web root is `public` and that `php artisan route:clear` / `php artisan config:clear` don’t need to be run after changing `.env`.
- **Empty or missing `public`:** In SSH run `ls -la ~/application/public` – you should see `index.php`. If not, re-copy the app into `~/application` or upload a built zip from your Mac.
