# Sourav's Stamp

Website for **Sourav Sign Stamp Store**: custom stamps, rubber stamps, name plates, sign boards and accessories. Visitors will browse a catalog and place orders; the owner will manage everything from an admin panel.

> **Status: Phase 1 only (foundations).** No customer pages, product management, checkout or database schema exist yet. See "Development phases".

## Technologies

- Core PHP 8.1+ (no framework)
- MySQL / MariaDB via PDO
- HTML5, CSS3, vanilla JavaScript
- Apache (XAMPP for local development)

## Local setup (XAMPP)

1. Install XAMPP (PHP 8.1 or newer) and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Place the project folder at `C:\xampp\htdocs\souravs-stamp` (macOS/Linux: `htdocs/souravs-stamp`).
3. Make sure Apache reads `.htaccess` files: in `xampp/apache/conf/httpd.conf` the `htdocs` directory should have `AllowOverride All`, and `mod_rewrite`, `mod_headers` should be enabled (they are by default). Restart Apache after changes.
4. Open http://localhost/phpmyadmin and create a database named `souravs_stamp` (collation `utf8mb4_unicode_ci`). Tables arrive in a later phase.
5. Create the `logs/` folder in the project root (it is gitignored except `.gitkeep`).
6. Open http://localhost/souravs-stamp/ in your browser.

## Configuring the database connection

Defaults in `config/database.php` match a fresh XAMPP (`root`, empty password, database `souravs_stamp`). To use different credentials, create **`config/database.local.php`** (it is gitignored and never committed):

```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_strong_password');
```

To change environment or URL path, create **`config/config.local.php`**:

```php
<?php
define('APP_ENV', 'production');   // 'development' shows errors, 'production' hides them
define('BASE_URL', '');            // '' when the site runs at the domain root
// define('TRUST_PROXY_HTTPS', true); // only if your host terminates HTTPS at a proxy
```

On a live server, always use a dedicated database user with a strong password, never `root`.

## Folder structure

```
souravs-stamp/
├── index.php
├── pages/                  public pages (later phases)
├── admin/                  admin pages (later phases)
├── actions/                form handlers (POST)
│   └── admin/
├── config/
│   ├── config.php          bootstrap: constants, errors, session, includes
│   └── database.php        PDO connection
├── includes/
│   ├── header.php  navbar.php  footer.php
│   ├── functions.php       escaping, validation, CSRF, helpers
│   ├── auth.php            admin session/login helpers
│   ├── cart-functions.php  session cart helpers
│   └── admin/              header.php sidebar.php footer.php
├── assets/ (css, js, images, fonts, icons)
├── uploads/ (products, gallery)
├── database/               SQL schema files (tracked in Git)
├── logs/                   runtime logs (contents ignored by Git)
├── .htaccess
├── .gitignore
└── README.md
```

## Using the includes (example for a future page)

```php
<?php
$pageTitle = 'Catalog';
$navActive = 'catalog';
require_once __DIR__ . '/../includes/header.php';   // from /pages
?>
<h1>Catalog</h1>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

Every state-changing form must contain `<?= csrf_field() ?>`, and its action file must call `csrf_require()` (or `require_admin_post()` for admin actions) first. Always escape output with `e()`, and always use prepared statements.

## Security notes

- Sessions: strict mode, HttpOnly, SameSite=Lax, Secure flag on HTTPS, ID regenerated on login/logout.
- PDO with exceptions and real prepared statements; DB errors are logged, never shown.
- Output escaping (`e()`), CSRF tokens, password hashing (`password_hash`).
- `.htaccess` blocks dotfiles, `config/`, `database/`, `includes/`, `logs/`, SQL/backup/log files and PHP execution in `uploads/`.
- `APP_ENV = 'production'` turns off error display.
- Admin pages call `require_admin()` before any output.
- **Frontend code can never be fully hidden.** HTML, CSS and JS are sent to every visitor's browser. Minification makes code smaller, and obfuscation only makes it harder (not impossible) to read. Neither is real protection. Keep all sensitive logic, validation and secrets on the PHP server, and never put credentials or keys in frontend files.

## Production optimization (documented process)

Never minify or obfuscate files in your working project.

1. Copy the project to a separate folder (for example `dist/`, which is gitignored).
2. In the **copy**, create `.min` versions next to the originals, for example with free Node tools:
   `npx terser assets/js/main.js -c -m -o assets/js/main.min.js` and
   `npx clean-css-cli -o assets/css/style.min.css assets/css/style.css`
   (online minifiers also work).
3. With `APP_ENV = 'production'`, the `asset()` helper automatically serves `*.min.css` / `*.min.js` when present.
4. Remove development comments and `console.log` calls from the copy.
5. JavaScript obfuscation is optional and only for small non-critical scripts. Test thoroughly, since it can break code.
6. Upload the copy to the server. Do not edit the minified files by hand.

## Git setup

```bash
git init
git add .
git status        # verify no secrets are staged
git commit -m "Phase 1: project foundations"
git branch -M main
git remote add origin https://github.com/<you>/souravs-stamp.git
git push -u origin main
```

Create empty `.gitkeep` files in `logs/`, `uploads/products/` and `uploads/gallery/` so these folders exist in the repository.

**Commit:** all PHP/CSS/JS source, `README.md`, `.htaccess`, `.gitignore`, `database/*.sql` (schema), `.gitkeep` files, and example config files.
**Never commit:** `.env`, `config/*.local.php`, real credentials, logs, uploaded customer/product files, database dumps with real data, backups, `dist/`.

If you ever commit a secret by accident, change the password immediately. Removing the file in a later commit does not remove it from Git history.

## Development phases

- [x] **Phase 1:** project foundations, reusable includes, security and source-protection setup
- [ ] **Phase 2:** database schema, admin login
- [ ] **Phase 3:** public pages (home, catalog, product, about, contact, store location)
- [ ] **Phase 4:** cart and enquiry/order flow
- [ ] **Phase 5:** admin: products, categories, gallery, customers, orders, settings
- [ ] **Phase 6:** testing, production optimization, deployment

Phases 2 to 6 are planned only; nothing in them is implemented yet.