# SMP Contributor Network Project Context

- Repository: `https://github.com/mikeyperes/smp-contributor-network.git`, branch `main`.
- Plugin slug: `smp-contributor-network`. Entry point: `smp-contributor-network.php`; version also in `src/PluginRuntime/PluginMetadata.php`.
- Live source: the installed folder on herforward.com (server 236), `/home/herforward/public_html/wp-content/plugins/smp-contributor-network`, which is the Git working tree.
- Rules: the `hexa-plugin-guidelines` skill. HexaWP Core in `lib/` is never edited here.
- Test: `wp eval-file tests/boot-smoke.php` from the site root.
