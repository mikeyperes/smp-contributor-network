# Council pieces moved from Verified Profiles

Moved in 1.1.0 (Verified Profiles 8.3.0 removed them). Kept as the record of where each piece came from.

## Program settings

- Options group `contributor_network` on the Verified Profiles settings page (`register-acf-structure-theme-options.php`): `program_name`, `email`, `logo`, `loop_items` (four Elementor loop templates: press releases, articles written by, articles featured in, mentioned in article) and `pages` (badges, claim, apply, welcome).
- Second copy of the program fields in `register-acf-structures.php`: `contributor_network_program_name`, `contributor_network_program_email`, `contributor_network_program_logo`.

## User fields

- `price_leadership_council` and `is_council_member` on user profiles (`register-acf-user-profile.php`, listed in `src/ContentTypes/VerifiedProfileStructures.php`).

## Shortcodes

- `[contributor_network field="..."]` (`shortcodes.php`, catalogued in `settings-dashboard-shortcodes.php`).
- `[display_profile_council_banner]` (`shortcodes.php`).

## Checkout and dashboards

- WooCommerce price override for the council product, ID 14142 hard-coded (`snippet-woocommerce-base.php`, `woocommerce_adjust_price_for_products`).
- Council apply and checkout links in the profile dashboards (`dashboard-verified-profile.php`, `profile-manager-dashboard.php`), with the Her Forward URL hard-coded.

## Pages on herforward.com

- 14600 https://herforward.com/her-forward-leadership-council/
- 14586 https://herforward.com/her-forward-leadership-council/application-her-forward-leadership-council/
- 16463 https://herforward.com/her-forward-leadership-council/badges-her-forward-leadership-council/

## Found while listing

- `[display_profile_council_banner]` reads the field `council_member`, but the field is named `is_council_member`, so the banner never shows.
- Stored options hold two page key sets (`pages_verified_profiles_*` and `pages_page_verified_profiles_*`), both pointing at Verified Profile pages (13574, 14583), not at the council pages.
- `options_contributor_network_email` holds `123123`; the real address is in `options_contributor_network_program_email`.
