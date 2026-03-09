# Deploy G&M Autospares to SiteHost

Use this after you’ve pushed the latest code to GitHub (`git push origin main`).

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

## 2. On SiteHost – first-time setup (or if you’re cloning fresh)

**SSH into your SiteHost container/server**, then:

```bash
# Go to your web root (e.g. where you want the app). Example:
cd /home/youruser  # or wherever SiteHost puts your sites

# Clone from GitHub (use your repo URL)
git clone git@github.com:stevewebmaster/gmautoparts.git gmautoparts
cd gmautoparts
```

**If the repo is already there** (you’ve done this before), just pull:

```bash
cd /path/to/gmautoparts   # wherever you cloned it
git pull origin main
```

---

## 3. On SiteHost – install/update and configure

Run these in the project folder (e.g. `gmautoparts`):

```bash
# Install PHP dependencies (first time or after composer.json change)
composer install --no-dev --optimize-autoloader

# First time only: copy env and generate key
cp .env.example .env
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

## 4. Point the site at `public/`

- **Nginx:** Set `root` to `/path/to/gmautoparts/public;`
- **Apache:** Document root should be `.../gmautoparts/public`, or use a `.htaccess` in the parent that routes to `public/` (Laravel’s `public/.htaccess` handles this if the vhost points to `public`).

SiteHost may have a “Document root” or “Web root” setting in the panel – set it to the `public` folder inside your project.

---

## 5. Quick “update only” (after first deploy)

When you’ve already set up the app and only need to deploy new code:

```bash
cd /path/to/gmautoparts
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
