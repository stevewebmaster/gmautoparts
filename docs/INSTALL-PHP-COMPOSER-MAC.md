# Install PHP and Composer on macOS

Use these steps in your **Terminal** app. You’ll need an internet connection and (for Homebrew) your Mac password.

---

## Step 1: Install Homebrew

Homebrew is the standard way to install PHP and Composer on Mac.

**Run this in Terminal** (it will prompt for your password):

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

When it finishes, it will show you two lines to run to add Homebrew to your PATH, for example:

```bash
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

**Run those two lines** (or the exact ones the installer shows). Then close and reopen Terminal, or run:

```bash
eval "$(/opt/homebrew/bin/brew shellenv)"
```

Check it works:

```bash
brew --version
```

---

## Step 2: Install PHP

```bash
brew install php
```

This installs the latest PHP (e.g. 8.4). Check:

```bash
php -v
```

---

## Step 3: Install Composer

```bash
brew install composer
```

Check:

```bash
composer --version
```

---

## Step 4: Use them in your G&M project

From your project folder:

```bash
cd /Users/stevepeters/Files/GM
composer install
cp .env.example .env
php artisan key:generate
# Edit .env with your DB and mail settings, then:
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan make:filament-user
php artisan serve
```

Then open http://localhost:8000 and http://localhost:8000/admin.

---

## If you already have Homebrew

If `brew` works, skip Step 1 and run only:

```bash
brew install php composer
```

---

## Apple Silicon vs Intel

- **Apple Silicon (M1/M2/M3):** Homebrew installs to `/opt/homebrew`. The PATH lines above use that path.
- **Intel Mac:** Homebrew is usually at `/usr/local`. If the installer gives different PATH lines, use those instead.
