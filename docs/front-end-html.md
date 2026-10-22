# Front-end HTML

## Member banner (`[display_profile_council_banner]`)

```html
<span class="display_profile_council_banner"><i class="fas"></i><span>Her Forward Leadership Council Member</span></span>
```

- `.display_profile_council_banner i:before` draws the Font Awesome check icon (red, 19px); the rule prints once, with the first banner.
- The inner `<span>` holds the Member banner text setting.

Renderer: `src/Council/CouncilShortcodes.php`.
