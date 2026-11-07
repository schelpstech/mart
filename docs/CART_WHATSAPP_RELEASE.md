# Cart sharing and mobile navigation

Implemented locally on 2026-11-07. The destination for cart enquiries is **+44 7767918847** (`447767918847` in the WhatsApp URL). No production deployment was performed.

## Customer behaviour

The sidebar, cart page, and checkout offer **Send cart on WhatsApp** and **Copy list**. Guests can share without logging in. Opening the preview requests a fresh summary of the current session's cart. It shows each product/service name, available product SKU, quantity, unit price, line total, and items total.

The preview's **Open WhatsApp** link opens a prepared message for the customer to review and send. Cart contents and payment state are preserved. Sharing does not create an order, charge a payment, or consume a coupon. Checkout sharing includes the selected delivery/pickup option, validated zone fee, coupon discount, and final total. Before fulfilment is selected, the message says delivery/pickup and charges are to be confirmed. Requested service appointment details are included when supplied; addresses and account contact details are not included.

Long messages remain complete. When the encoded link exceeds the conservative 7,000-character threshold, the preview instructs the customer to copy the list and paste it into the store chat. Clipboard failure selects the message for manual copying. Empty carts disable sharing; invalid coupons, unavailable delivery zones, and invalid appointment date/time values return readable errors.

## Main implementation files

| File | Purpose |
| --- | --- |
| [whatsapp_helpers.php](../controller/whatsapp_helpers.php) | Approved number and message formatting. |
| [cart_share.php](../app/ajax/cart_share.php) | Session-scoped POST endpoint for fresh messages and WhatsApp links. |
| [cartShare.js](../view/assets/js/cartShare.js) | Preview, copy fallback, link, and request states. |
| [cartShare.php](../view/inc/cartShare.php), [cartShareDialog.php](../view/inc/cartShareDialog.php) | Shared customer controls and preview dialog. |
| [storefront.js](../view/assets/js/storefront.js), [storefront.css](../view/assets/css/storefront.css) | Drawer controls, keyboard support, mobile layout, and styles. |
| [manageCart.js](../view/assets/js/manageCart.js) | Cart updates, refreshes, buttons, counts, and quantity saves. |
| [store_helpers.php](../controller/store_helpers.php) | Shared product pricing and checkout totals; service-only carts have no delivery fee. |
| [accountMenu.php](../view/inc/accountMenu.php) | Consistent desktop/mobile account destinations. |

## Other corrections

- Store-section buttons expand using the full row, expose expanded state, and use accessible keyboard controls.
- Closed cart/menu drawers are inert. Open drawers lock page scrolling, contain keyboard focus, support Escape, and return focus to the opening control.
- The fixed bottom toolbar allows account menus to extend above it and uses larger labelled controls. Profile and Order History are available on mobile.
- The header includes the cart drawer once on all pages, including booking. Duplicate cart-count IDs were removed. Logo links use the PHP homepage.
- Checkout/booking offsets apply only on larger screens. Invalid nested policy markup that contributed to mobile overflow was corrected.
- The old theme's fake add-to-cart/sidebar mutations were removed. Application cart writes are queued, controls are refreshed from the server, and sharing is disabled while changes are being saved. Product detail uses its chosen quantity. Infinite-scroll cards synchronize their cart state.
- Product cards, quick view, detail prices, and add-to-cart use one sale-price helper. A positive sale price below the regular price is charged/displayed; an invalid higher sale price is ignored. Existing saved cart prices continue to determine checkout and message totals.
- Product links from cart lines use product IDs, matching the detail page.
- The duplicate inline checkout fulfilment controller was removed; the shared validator handles the controls.

## Local database repair

The interrupted local restore had omitted primary keys and auto-increment attributes on eleven identity columns. [repair_local_identity_keys.php](../scripts/repair_local_identity_keys.php) preflights every existing ID for uniqueness/positivity before changing any table. It is CLI-only and refuses non-local database hosts. Dry-run is the default; `--apply` performs the repair.

A complete local SQL backup was saved outside the web root under the current Windows user's temporary directory, in `mart-local-backups`, before the repair. The backup filename starts with `mart_before_cart_whatsapp_20261107_150123`. All eleven identities were repaired without renumbering records. Legacy zero dates were retained using a connection-only compatibility setting for the DDL; server-wide SQL mode was not changed. A repeat check reports zero tables needing repair.

Foreign keys, business uniqueness constraints, and unrelated schema differences were not inferred or recreated. The existing admin role mismatch and other observations in the original app guide still need their own scoped work. This local repair script is not a production database installer.

## Validation

- `php tests/cart_whatsapp.php --http`: **32 checks passed**. Covers pricing, text normalization, empty carts, message encoding and destination, quantity changes, guest sharing, authenticated checkout, pickup, delivery zones, coupons, mixed/service-only carts, appointment validation, and preservation of orders/coupon usage.
- The HTTP suite creates disposable catalog, coupon, zone, user, session, and cart fixtures and removes them in `finally`. It does not contact WhatsApp, send registration/order email, or initiate a payment.
- Browser verification used an isolated `127.0.0.1` session so the user's `localhost` session was preserved. Checked mobile layouts at 360, 390, and 430 widths plus desktop at 1280; section expansion, readable account dropdowns, booking cart availability, cart quantity totals, copying, guest preview, and authenticated pickup preview were exercised.
- Disposable browser QA credentials and their carts were removed after logout. Final database checks found 500 products, 10 orders, one administrator, no QA accounts, and no test coupons/zones.
- All 107 first-party PHP files passed syntax checks; all six changed JavaScript files passed `node --check`; `git diff --check` passed. Changed storefront assets have a version query to prevent older cached cart/menu scripts from remaining active after deployment. Actual message delivery, live Stripe payments, and SMTP delivery were not exercised.

Screenshots: [mobile cart preview](screenshots/whatsapp-cart-mobile.jpg), [mobile account menu](screenshots/mobile-account-menu.jpg), [desktop checkout preview](screenshots/whatsapp-checkout-desktop.jpg).

## Deployment and rollback

Deploy the modified PHP pages/handlers/helpers and the new shared partials/assets together, using the repository diff as the file list. Both storefront heads load the new stylesheet; the shared footer loads the new scripts and dialog. No additional Composer/npm dependencies or WhatsApp API credentials are required. Existing cart, service, delivery, and coupon tables must already be installed and have working identities.

Use the CLI local identity repair only for a local database that exhibits the missing-key problem. A production database with its proper keys does not need this local repair. Keep SQL dumps and database backups outside the public deployment; `products.mysql.sql` is the earlier import aid, not a runtime dependency. Tests and local maintenance scripts are not required for serving the storefront.

After deployment, verify a guest cart, login/cart retention, product quantity, cart preview, and selected checkout fulfilment. Check the WhatsApp link and copy function on an actual phone. The store's staff should confirm the first real customer enquiry arrives correctly.

To roll back the feature, restore the previous versions of modified runtime files and remove the new runtime files after their includes have been reverted. The feature adds no orders or new persistent business tables. Retain the repaired local primary keys; dropping them would reintroduce the original import failure. The local SQL backup is available if a complete return to the pre-work database snapshot is deliberately needed.

Existing product/service image files missing from the local checkout and the lack of selected size/colour storage remain separate issues. Automatic stock reservation and a calendar availability engine are also outside this cart-sharing change.
