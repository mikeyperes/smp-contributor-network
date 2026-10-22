# Asset loading

- Admin: no separate CSS or JS files. The Pages tab prints its small Create page script inside the tab, so it runs only when that tab is open.
- Front end: no enqueued files. The member banner prints its one CSS rule once, with its first output, only on pages that show the banner.
- Check: open the home page and a profile page and confirm no `smp-contributor-network` asset is requested.
