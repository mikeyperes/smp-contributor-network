# SMP Contributor Network

> Feature base for HWS Skills. Read before building on this plugin; use or
> extend these features instead of rebuilding them.

**Purpose:** runs a publication's contributor network program (on Her Forward, the Her Forward Leadership Council) and its Become a Contributor page. **Admin:** WP Admin → Contributor Network. **Depends on:** WooCommerce for the council price (optional); SMP Publication Integration for the publication name in the Become a Contributor template; HexaWP Core is bundled.

Built on the Hexa plugin foundation (`hexa-plugin-guidelines`), with HWS Base Tools as the reference build.

## Design examples and styling

- Member banner HTML and selectors: [docs/front-end-html.md](docs/front-end-html.md).
- Asset scoping: [docs/asset-loading.md](docs/asset-loading.md).

## Features

### Program settings
- **Does:** one place for the program's name, email, logo, member banner text, council product, the three council pages (council, application, badges), the profile loop templates and profile pages. Nothing site-specific is written in the code.
- **Switch:** WP Admin → Contributor Network → Program. Stored as ACF-layout options `options_contributor_network_*` (field keys kept from Verified Profiles, so earlier values still load). The older second copy (`contributor_network_program_name|email|logo`) is shown on the same tab and still read as a fallback for the program name.
- **Code:** `src/Council/CouncilFields.php`, `src/Council/CouncilSettings.php`, `src/AdminDashboard/ProgramTab.php`.

### Council user fields
- **Does:** "Price Leadership Council" (`price_leadership_council`) and "Is Council Member" (`is_council_member`) on user profiles, shown to administrators. Field keys `field_65a45a45cdee7` and `field_65a8aeaa48c6f` unchanged.
- **Code:** `src/Council/CouncilFields.php`.

### `[contributor_network]`
- **Use:** `[contributor_network field="program_name|email|logo|member_banner_text|council_product_id|loop_items.<name>|pages.<name>|council_pages.landing|council_pages.application|council_pages.badges" size="thumbnail"]`. `logo` prints an image URL at `size`; page and loop fields print the post ID; `pages.<name>` also reads the older `page_<name>` keys.
- **Code:** `src/Council/CouncilShortcodes.php`.

### `[display_profile_council_banner]`
- **Does:** on a profile page, shows the member banner when the profile's linked user (`contributor_profile`) has "Is Council Member" on. Text: the Member banner text setting, else "<program name> Member".
- **Style:** `.display_profile_council_banner`; HTML in [docs/front-end-html.md](docs/front-end-html.md).
- **Code:** `src/Council/CouncilShortcodes.php`.

### Council price at checkout
- **Does:** when a signed-in user with a council price buys the council product, the cart uses their price.
- **Switch:** Program → Council product; per user, Price Leadership Council.
- **Code:** `src/Council/CouncilCheckout.php` (`woocommerce_before_calculate_totals`).

### Council apply link in the profile dashboard
- **Does:** adds "Apply for <program name>" linking to the Application page to the Verified Profiles dashboard's Additional Links.
- **Extend:** hooked to Verified Profiles' `smp_verified_profiles_dashboard_links` action.
- **Code:** `src/Council/CouncilCheckout.php`.

### Become a Contributor page
- **Does:** a generic page explaining how to pitch and write for the publication; the publication name, email and editorial guidelines link come from SMP's `[smp_company]` and `[smp_publication_page]`.
- **Switch:** WP Admin → Contributor Network → Pages: shows the linked page (found at `/become-a-contributor/` or `/write-for-us/`, or the one created there; `smp_contributor_network_pages` option) or a Create page button (creates a draft).
- **Use:** `[smp_contributor_network_page_template type="become_contributor"]`.
- **Code:** `src/Pages/PageTypes.php`, `src/Pages/PagesModule.php`, `src/Pages/templates/become_contributor.html`.

### Contributor Login page
- **Does:** the page where contributors sign in (`/contributor-login/`), linked from the top bar of every publication. Signed out it shows WordPress's sign-in form and a forgot-password link; signed in it links to writing a new article and to the dashboard.
- **Use:** `[smp_contributor_login]` (inside `[smp_contributor_network_page_template type="contributor_login"]`); `wp smp-contributor-pages ensure` publishes every missing Contributor Network page.
- **Code:** `src/Pages/PageTypes.php`, `src/Pages/PagesModule.php`, `src/Pages/templates/contributor_login.html`.

### Page links from other plugins

- **What:** HWS Base Tools and SMP Publication Integration pages can link to Become a Contributor and Contributor Login with `[hws_page_link]` and `[hws_if page]`.
- **Code:** `src/Pages/PagesModule.php` (`hexa_site_structure_page_id` filter).

## Internals

- Admin page with Core tabs in groups: Overview; Program (Program, Pages); Operations (Update Center); Hexa Core. Filters `smp_contributor_network_dashboard_tabs` and `smp_contributor_network_render_dashboard_tab`; a tab loads only when opened (`src/AdminDashboard/AdminPage.php`).
- Update Center: Core's GitHub updater with its Git Report, and Core's bundled-package panel (`src/Updates/UpdateCenter.php`).
- HexaWP Core bundled at `lib/hexa-wordpress-plugin-core`; see `HEXA_PLUGIN_CORE_LIBRARY.md`.
- Identity and version: `src/PluginRuntime/PluginMetadata.php` and the plugin header, always equal.
- Where the council pieces came from: [docs/council-pieces-in-verified-profiles.md](docs/council-pieces-in-verified-profiles.md).

## Test

`wp eval-file tests/boot-smoke.php` boots the plugin without activating it and renders every tab.

## Changelog

### 1.3.1

- Bundles Hexa WP Core 3.41.0.

### 1.3.0

- Become a Contributor and Contributor Login default texts rewritten with HWS Base Tools' building blocks; each links to the other, and the Editorial Guidelines link shows only when that page exists.
- Other plugins' page links ([hws_page_link key="become_contributor"], [hws_if page="contributor_login"]) now find this plugin's pages through Hexa WP Core.
- Bundles Hexa WP Core 3.40.1.

### 1.2.0

- New Contributor Login page with the sign-in form, and `wp smp-contributor-pages ensure` to publish missing pages. Bundles Hexa WP Core 3.36.0.

### 1.1.0
- Leadership Council moved in from SMP Verified Profiles: program settings and the older copy, the two user fields, `[contributor_network]`, `[display_profile_council_banner]`, the council checkout price and the dashboard apply link. Her Forward's address and product number are now settings.
- Banner fix: it read `council_member` instead of `is_council_member`, so it never showed.
- Become a Contributor page type and template (moved out of SMP Publication Integration).
- Bundles Hexa WP Core 3.34.1.
