# G&M Mini-app – Add parts & vehicles from phone

The mini-app lets the client add **parts** and **vehicles** (Now Dismantling) from their phone: take photos, enter details, and the items appear on the main website.

## Test on your phone today

1. **Use HTTPS** — Open the site with `https://` (required for camera/file access and a smooth install on most phones).
2. **Set the PIN on the server** — In `~/container/application/.env` on SiteHost: `MINIAPP_PIN=yourpin`, then run `php artisan config:clear` and `php artisan config:cache`.
3. **Open the login URL** in Safari (iPhone) or Chrome (Android):  
   `https://yourdomain.co.nz/app/login`
4. Enter the PIN → you should see **Add a part** / **Add a vehicle**.
5. **Add to Home Screen** (optional but recommended):
   - **iPhone (Safari):** Share → **Add to Home Screen**
   - **Android (Chrome):** Menu → **Add to Home screen** / **Install app**
6. **After saving**, confirm on the main site: **Parts** catalogue and **Now Dismantling** should show the new items (they are created visible by default).

If anything fails, see **Troubleshooting** below and [DEPLOY-SITEHOST.md](DEPLOY-SITEHOST.md) (one app directory only: `~/container/application`).

## Setup

1. **Set a PIN** in `.env`:
   ```env
   MINIAPP_PIN=1234
   ```
   Use a PIN only the client knows. If `MINIAPP_PIN` is empty, the mini-app is disabled (404).

2. **Storage link** (for uploads):
   ```bash
   php artisan storage:link
   ```
   Parts and vehicles photos are stored in `storage/app/public/parts` and `storage/app/public/vehicles`.

## URLs

- **Login:** `https://yoursite.co.nz/app/login`
- **Dashboard (after login):** `https://yoursite.co.nz/app`

Share the login URL and PIN with the client. They enter the PIN once per browser session.

## On their phone

1. Open the login URL in the browser (Safari, Chrome, etc.).
2. Enter the PIN and tap **Continue**.
3. Choose **Add a part** or **Add a vehicle**.
4. Fill the form and add photos (camera or gallery). Tap **Save**.
5. New parts appear in the Parts catalogue; new vehicles in Now Dismantling.

## Add to Home Screen (PWA)

On iPhone (Safari): **Share → Add to Home Screen**.  
On Android (Chrome): **Menu → Add to Home screen** or **Install app**.

The manifest is at `/app-manifest.json` so the mini-app can open like an app.

## Server 500 when saving a part or vehicle

1. **Check the log** on the server: `tail -n 60 ~/container/application/storage/logs/laravel.log`
2. **Storage link & permissions:**  
   `php artisan storage:link`  
   `chmod -R 775 storage bootstrap/cache`
3. **Skip image resizing** (common fix if PHP has no GD/Imagick, **memory errors**, or Intervention fails): in `.env` add  
   `MINIAPP_OPTIMIZE_UPLOADS=false`  
   then `php artisan config:clear` and `php artisan config:cache`.
4. **“Allowed memory size … exhausted” in `intervention/image` / GD:** PHP’s limit (often **128M**) is too low for big camera photos. Either use **`MINIAPP_OPTIMIZE_UPLOADS=false`** (simplest), or ask SiteHost to raise **`memory_limit`** for PHP (e.g. **256M**), and/or set **`MINIAPP_IMAGE_MEMORY_LIMIT=512M`** in `.env` (only works if the host allows `ini_set` for memory).
5. **Database:** run `php artisan migrate --force` so `parts.images` and `vehicles.images` exist.

## Custom home-screen icon (later)

You can replace `public/icons/miniapp-icon.svg` or add PNGs (e.g. 192×192 and 512×512) and list them in `public/app-manifest.json` under `icons` for best results on iOS and Android.

## Image optimization

Uploads from the mini-app are automatically optimized before saving (unless `MINIAPP_OPTIMIZE_UPLOADS=false`):

- **Max width:** 1200px (height scales to keep aspect ratio).
- **Format:** JPEG at 82% quality.

This keeps file sizes down and avoids filling the server. If optimization fails (e.g. corrupt image), the original file is stored instead.

---

## Troubleshooting: "Invalid PIN" when PIN is in .env

1. **Use the app directory that the site actually runs from.** The web server (Nginx) only uses one directory. On SiteHost that is **`~/container/application`**. All `php artisan` and `.env` must be in that directory. If you have another folder (e.g. `~/gmautoparts`), the PIN there is ignored. See [DEPLOY-SITEHOST.md](DEPLOY-SITEHOST.md) (“One app directory only”).

2. **Check that Laravel sees the PIN** (run this **in the app directory**, e.g. `cd ~/container/application`):
   ```bash
   php artisan tinker --execute="echo config('miniapp.pin') ? 'PIN is set (' . strlen(config('miniapp.pin')) . ' chars)' : 'PIN is EMPTY';"
   ```
   If it says **PIN is EMPTY**, then either `.env` has no `MINIAPP_PIN` or config is cached from before you added it. Fix: add `MINIAPP_PIN=1234` to **`~/container/application/.env`**, then run:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

3. **No spaces in `.env`:** Use `MINIAPP_PIN=1234` not `MINIAPP_PIN = 1234` (spaces can cause issues).

4. If you see **"Mini-app PIN is not set on the server"** after submitting, the config value is empty at runtime—follow step 2.

---

## Security

- Access is protected by the PIN only (no user accounts).
- PIN is stored in `.env`; do not commit it. Change it if it is ever shared.
- New parts and vehicles are created with `is_visible = true` so they appear on the site immediately. To hold for approval, change the controller to set `is_visible => false` and approve in Admin.
