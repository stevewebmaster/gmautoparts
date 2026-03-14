# G&M Mini-app – Add parts & vehicles from phone

The mini-app lets the client add **parts** and **vehicles** (Now Dismantling) from their phone: take photos, enter details, and the items appear on the main website.

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

## Security

- Access is protected by the PIN only (no user accounts).
- PIN is stored in `.env`; do not commit it. Change it if it is ever shared.
- New parts and vehicles are created with `is_visible = true` so they appear on the site immediately. To hold for approval, change the controller to set `is_visible => false` and approve in Admin.
