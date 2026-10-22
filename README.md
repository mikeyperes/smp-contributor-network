# SMP Contributor Network

> Feature base for HWS Skills. Read before building on this plugin; use or
> extend these features instead of rebuilding them.

**Purpose:** runs a site's contributor network program (on Her Forward, the Her Forward Leadership Council). **Admin:** WP Admin → Contributor Network. **Depends on:** nothing; HexaWP Core is bundled.

Built on the Hexa plugin foundation (`hexa-plugin-guidelines`), with HWS Base Tools as the reference build.

## Features

No site-facing features yet. The council pieces that still live in Verified Profiles, and will move here, are listed in [docs/council-pieces-in-verified-profiles.md](docs/council-pieces-in-verified-profiles.md).

## Internals

- Admin page with Core tabs in groups: Overview, Update Center, Hexa Core. Filters `smp_contributor_network_dashboard_tabs` and `smp_contributor_network_render_dashboard_tab`; a tab loads only when opened (`src/AdminDashboard/AdminPage.php`).
- Update Center: Core's GitHub updater with its Git Report, and Core's bundled-package panel (`src/Updates/UpdateCenter.php`).
- HexaWP Core bundled at `lib/hexa-wordpress-plugin-core`; see `HEXA_PLUGIN_CORE_LIBRARY.md`.
- Identity and version: `src/PluginRuntime/PluginMetadata.php` and the plugin header, always equal.

## Test

`wp eval-file tests/boot-smoke.php` boots the plugin without activating it and renders every tab.
