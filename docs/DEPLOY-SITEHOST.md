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

**SSH into your SiteHost container**, then:

```bash
# On Cloud Containers the app must live in /application
cd /

# If /application is empty (new container): clone so repo root becomes /application
# (Back up existing /application first if it has content you want to keep.)
mv application application.bak 2>/dev/null || true
git clone git@github.com:stevewebmaster/gmautoparts.git application
cd /application
```

**If the repo is already in `/application`** (you’ve done this before), just pull:

```bash
cd /application
git pull origin main
```

---

## 3. On SiteHost container – install/update and configure

Run these in the project folder (**`/application`** on the container):

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

## 4. Set web root to `/application/public`

On **SiteHost Cloud Containers** the web root must be Laravel’s `public` folder:

- **Nginx** (config under `/config`): set `root` to **`/application/public;`**
- **Apache** (config under `/config`): set **DocumentRoot** to **`/application/public`**

SiteHost may also expose a “Document root” or “Web root” in the panel – set it to **`/application/public`**.  
Do **not** point the web root at `/application` (project root); the site will break or expose config files.

---

## 5. Quick “update only” (after first deploy)

When you’ve already set up the app and only need to deploy new code:

```bash
cd /application
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

---

## Troubleshooting

- **500 error:** Check `storage/logs/laravel.log`. Ensure `storage` and `bootstrap/cache` are writable (775 and owned by the web server user).
- **DB connection:** Confirm `.env` has the correct SiteHost database host, name, user, and password.
- **Admin not loading:** Ensure the web root is `public` and that `php artisan route:clear` / `php artisan config:clear` don’t need to be run after changing `.env`.
