# G&M Autospares – Client Guide

This guide explains how to manage your website content using the Admin panel.

## Logging in

- **Admin URL:** `https://yoursite.co.nz/admin`
- Log in with the email and password set up by your developer.
- If you forget your password, use “Forgot password” on the login page (if configured).

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
   - **Condition** – e.g. Used, Refurbished.
   - **Price** – optional; leave blank for “Enquire”.
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

- **Categories** (e.g. ENGINE, BRAKES) are under **Admin** → **Catalogue** → **Part categories**.
- To add a **subcategory** to a category: open that category, go to the **Subcategories** tab, click **New**, enter Name and Slug, then save.
- You can reorder categories and subcategories using the **Sort order** field (lower numbers appear first).

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

## Quick reference

| Task                 | Where in Admin                          |
|----------------------|-----------------------------------------|
| Add/edit parts       | Catalogue → Parts                       |
| Add/edit vehicles    | Catalogue → Now Dismantling             |
| Categories           | Catalogue → Part categories             |
| Subcategories        | Part category → Subcategories tab       |
| Homepage slider      | Content → Homepage slider               |
| About / Contact text | Content → Pages                         |

If you need new categories or subcategories added in bulk, or changes to the design or email setup, contact your developer.
