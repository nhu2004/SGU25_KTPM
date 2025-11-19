# Product Catalog — Phase 1 (Trimmed)

This package is a **Phase 1 (Product Catalog only)** cut of the original project.

## Included (Phase 1)
- Public catalog pages: Home, Products list, Product detail, Brand/Category listing, Search, About/Blog/Article/Contact.
- Database config kept at `admin/config/config.php` and `admin/format/format.php` for shared helpers.
- All assets required for rendering catalog (CSS/JS/images).

## Removed/Disabled (Defer to Phase 2+)
- Cart, Checkout, Payment, Account (Login/Register/My Account), Order pages.
- Action handlers under `pages/handle/`.
- Admin application UI (`admin/pages`, `admin/modules`, etc.).

## Routing
The router `pages/main.php` has been simplified to only allow catalog routes.
Navbar links to Cart/Login are replaced with static text indicating *Phase 2*.

## How to run
- Import the provided `dbperfume.sql` into MySQL (if needed).
- Serve this folder with PHP (e.g. `php -S localhost:8080` in the project root).
- Open `http://localhost:8080/index.php`.

## Notes
If you later enable Cart/Checkout/Auth, restore the removed files or merge from the original repo.
