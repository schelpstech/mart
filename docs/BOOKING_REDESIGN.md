# Salon booking redesign

Implemented locally on 2026-11-07. Preview: `http://localhost/mart/view/booking.php`.

## Customer experience

- Choose services using large selectable cards, search, and All/Selected filters. Prices and readable durations appear before checkout.
- Review selected services, approximate duration, and the running services total in a desktop summary or the persistent mobile action bar.
- Choose a preferred date and time in UK time. The page explains that the salon must confirm availability; this does not reserve calendar capacity.
- Selected services and the preferred appointment survive the guest sign-in step and populate checkout. A service detail page can preselect its service with `booking.php?service=ID`.
- Validation keeps entered choices and shows actionable field errors. Native fields and server validation support submission without JavaScript.

The design uses the existing PHP/session/cart architecture and plum storefront palette. It does not depend on service photos, several of which are missing locally. All nine active local services currently share one category, so filters deliberately use selection and search instead of misleading category names.

## Data and security behavior

- Booking and checkout require a session-bound CSRF token. Requests use server-side validation even when browser checks are bypassed.
- Service IDs must be valid scalar positive integers. Every selected service must still be active; a partially unavailable selection is rejected together.
- Service prices are loaded from the active catalog. Submitted prices and cart IDs do not control charges or cart ownership.
- Dates and times must be real future appointments in `Europe/London`, including validation of times skipped by daylight saving.
- Catalog text is escaped in HTML and inserted using text nodes in JavaScript. Public booking errors do not expose database exception details.
- Saving a selection updates the current session/user cart transactionally. It preserves product lines and deliberate existing service quantities, removes deselected service lines, and does not increment quantities on repeated submissions.
- Existing authentication and payment processing remain in place. No schema migration is required.

## Release files

Deploy these together so forms and handlers share the same CSRF contract:

`controller/booking_helpers.php`, `controller/cart.class.php`, `app/bookingHandler.php`, `app/orderHandler.php`, `view/booking.php`, `view/checkout.php`, `view/viewservice.php`, `view/inc/head.php`, `view/inc/footer.php`, `view/assets/css/booking.css`, and `view/assets/js/bookingform.js`.

CSS and JavaScript references have cache versions. Customers with a checkout form opened before deployment should reload it if prompted that the form has expired. Rollback requires reverting the same set together; this change does not modify database structure or historical orders.

## Verification

- `C:\Ampps\php\php.exe tests/booking_flow.php --http`: 31 checks, covering validation, escaping, unavailable services, CSRF failures, cart ownership, server prices, repeated selection, product preservation, and appointment retention into authenticated checkout.
- `C:\Ampps\php\php.exe tests/cart_whatsapp.php --http`: 32 existing cart/WhatsApp regression checks.
- Targeted PHP lint, JavaScript syntax, and Git whitespace checks.
- Chrome desktop at 1440px and mobile at 360px, 390px, and 430px: search, selection filters, keyboard selection, live totals, required consent, submission, appointment retention, and action-bar placement. Disposable browser selections and HTTP fixtures were removed after testing.

No real payment, email delivery, production deployment, or exhaustive application security audit was performed. Live appointment capacity/staff scheduling is a separate capability.

## Screenshots

- [Desktop booking](screenshots/booking-desktop.jpg)
- [Mobile selection](screenshots/booking-mobile.jpg)
- [Mobile preferred time](screenshots/booking-mobile-time.jpg)
