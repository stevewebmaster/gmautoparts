# G&M Autospares – Client Guide

This guide explains how to manage your website content using the Admin panel.

## Logging in

- **Admin URL:** `https://yoursite.co.nz/admin`
- Log in with the email and password set up by your developer.
- If you forget your password, use “Forgot password” on the login page (if configured).

---

## The dashboard

The page you land on after logging in is your dashboard. It shows:

- **Orders to pack** — paid for, not yet sent. Click it to see them.
- **Parts on hold** — reserved for a customer.
- **Parts on the site** and **Vehicles dismantling** — what the public can see.
- **Google Shopping** — how many parts are being sent to Google, and what is
  keeping the rest out.

Every number is a link to the list behind it.

### The Google Shopping panel

Google will only list a part if it has **a photo**, **a price**, and **a freight
size**. The panel counts the parts missing each of those and links straight to
them, so you can work through the list. To measure parts in the yard, use the
Parts Loader app: **Manage Stock → Needs freight size**. Parts marked *Quote only*, *Hidden*, or
*Sold* are left out on purpose — those are fine.

You can see the same thing per part: the **Google** column on the Parts list says
whether each part is in the feed, or what it is missing.

**One thing this panel cannot tell you:** whether Google has *approved* each
part. It shows what your website sends. If Google rejects something, that shows
up in Google Merchant Center, not here.

---

## Adding and editing parts

1. Go to **Admin** → **Catalogue** → **Parts**.
2. Click **New part**.
3. Fill in:
   - **Title** – e.g. “Front brake caliper”
   - **Slug** – usually auto-filled from the title (used in the part’s URL).
   - **Category** – choose from the main list (e.g. BRAKES, ENGINE).
   - **Subcategory** – optional (e.g. Brake Caliper). Add subcategories under **Part categories** if needed.
   - **Description** – optional details about the part.
   - **Stock number** – your internal reference.
   - **Condition** – e.g. Used, Reconditioned. Type **New** for new parts and Google lists them as new; anything else shows as used.
   - **Price** – **including GST**: what the customer pays. Optional; leave blank for “Enquire”.
   - **Freight size** – the satchel or box it goes in, same as eDespatchIT. Needed to sell it online.
   - **Make / Model / Year** – vehicle compatibility (optional).
   - **Vehicle** – link to a “Now Dismantling” vehicle if the part comes from one.
   - **Images** – upload one or more photos (drag to reorder). First image is the main one.
   - **Visible** – turn off to hide the part from the site without deleting it.
   - **Featured** – turn on to show it in “Featured parts” on the homepage.
4. Click **Save** (or **Save and create another**).

To **edit** a part, open **Parts**, click the part, change the fields and save.

---

## Adding and editing “Now Dismantling” vehicles

1. Go to **Admin** → **Catalogue** → **Now Dismantling**.
2. Click **New**.
3. Fill in:
   - **Make** – e.g. Toyota, Holden.
   - **Model** – e.g. Hilux, Commodore.
   - **Year** – e.g. 2015.
   - **Engine** – optional (e.g. 2.8 Diesel).
   - **Transmission** – optional (e.g. Auto, Manual).
   - **Stock number** – your reference.
   - **Notes** – optional.
   - **Images** – upload photos of the vehicle.
   - **Visible** – turn off to hide from the “Now Dismantling” page.
4. Click **Save**.

To add **parts** to this vehicle: open the vehicle in Admin, go to the **Parts** tab, then **New** and create a part (the vehicle will be linked automatically). You can also link existing parts to the vehicle by editing the part and choosing this vehicle.

---

## Managing part categories and subcategories

- The site comes pre-loaded with part categories and subcategories from the old website (e.g. ENGINE, GEARBOX, DOOR, LIGHTS). You can add new categories and subcategories anytime.
- **Categories** are under **Admin** → **Catalogue** → **Part categories**. Click **New** to add a category, or open an existing one to edit.
- To add a **subcategory** to a category: open that category, go to the **Subcategories** tab, click **New**, enter Name and Slug (slug is auto-filled from the name), then save.
- You can also manage all subcategories in one place: **Admin** → **Catalogue** → **Part subcategories**.
- Use the **Sort order** field (lower numbers appear first) to control the order categories and subcategories appear on the site.

---

## Managing the homepage slider

1. Go to **Admin** → **Content** → **Homepage slider**.
2. Click **New** to add a slide.
3. For each slide set:
   - **Title** – main heading on the slide.
   - **Subtitle** – optional line of text below the title.
   - **Image** – upload the slide image (recommended size: about 1200×400 px or similar).
   - **Link URL** – optional (e.g. `/parts` or an external URL).
   - **Link text** – optional button text (e.g. “Browse parts”).
   - **Sort order** – order of slides (0, 1, 2…).
   - **Active** – turn off to hide a slide without deleting it.
4. Click **Save**.

Slides rotate automatically on the homepage. Reorder by changing **Sort order** and saving.

---

## Editing page content (About, Contact)

1. Go to **Admin** → **Content** → **Pages**.
2. Click the page you want to edit (e.g. **About**, **Contact**).
3. Change **Title** and **Content** as needed. You can use the editor toolbar for bold, lists, links, etc.
4. **Meta description** is used for search engines; a short summary of the page is enough.
5. Click **Save**.

- **About** – content for the “About Us” page.
- **Contact** – intro text shown above the contact form. The form itself (name, email, phone, message) is fixed; form submissions are sent to the admin email set in the server configuration.

---

## Contact form and part enquiry emails

- **Contact form** (Contact page) and **Enquire now** (on each part) send emails to the address configured on the server (e.g. in `.env` as `MAIL_FROM_ADDRESS` or `ADMIN_EMAIL`).
- Ensure your host has mail (SMTP) set up so these emails are delivered. Your developer can configure the exact address and mail settings.

---

## Mini-app (add parts & vehicles from your phone)

If your developer has set up the **mini-app**, you can add parts and vehicles from your phone:

1. Open the mini-app URL (e.g. `https://yoursite.co.nz/app/login`) in your phone’s browser.
2. Enter the **PIN** you were given and tap **Continue**.
3. Choose **Add a part** or **Add a vehicle**.
4. Fill in the details and add at least one photo (you can use the camera). Tap **Save**.
5. The part or vehicle will appear on the main website.

You can **Add to Home Screen** (iPhone: Share → Add to Home Screen; Android: menu → Add to Home screen) so the mini-app opens like an app. The PIN is set by your developer; ask them to change it if needed.

---

## Quick reference

| Task                 | Where in Admin                          |
|----------------------|-----------------------------------------|
| See how you are doing | Dashboard (the page you land on)        |
| Check Google Shopping | Dashboard → Google Shopping             |
| Add/edit parts       | Catalogue → Parts                       |
| Add/edit vehicles    | Catalogue → Now Dismantling             |
| Categories           | Catalogue → Part categories             |
| Subcategories        | Part category → Subcategories tab       |
| Homepage slider      | Content → Homepage slider               |
| About / Contact text | Content → Pages                         |

If you need new categories or subcategories added in bulk, or changes to the design or email setup, contact your developer.
