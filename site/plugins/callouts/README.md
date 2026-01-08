# Callouts for Kirby

Bring GitHub-style callouts (a.k.a. admonitions) to Kirby using the familiar `[!TYPE]` blockquote syntax.
Your editors keep writing Markdown/KirbyText; your site gets polished, theme-ready callouts.

## Motivation

-   Kirby ships with great KirbyText (Markdown + KirbyTags) support, but callouts usually mean hand-written HTML or block blueprints.
-   Documentation platforms such as GitHub, Svelte, and Docusaurus already rely on `[!TYPE]` callouts, so let Kirby join the club.
-   Works out of the box with optional themes and still respects custom callout types your team invents.

## Installation

### Composer

```bash
composer require lemmon/kirby-callouts
```

### Git Submodule

```bash
git submodule add https://github.com/lemmon/kirby-plugin-callouts.git site/plugins/callouts
```

### Manual

[Download the plugin](https://api.github.com/repos/lemmon/kirby-plugin-callouts/zipball) and extract it to `/site/plugins/callouts`.

## Usage

```php
<?= css($kirby->plugin('lemmon/callouts')->asset('callouts-github.css')->url()) ?>
```

Keep writing Markdown/KirbyText as usual-whether you’re in a textarea field or a markdown block:

```markdown
> [!TIP]
> Add a handy tip right inside your content.
```

## Features

-   Turn `> [!TYPE]` blocks into callout wrappers automatically.
-   Supports NOTE, TIP, IMPORTANT, WARNING, CAUTION, and any custom type (e.g. `[!CHALLENGE]` -> `callout--challenge`).
-   Configurable wrapper (`div` or `blockquote`) and CSS class prefix.
-   Optional HTML header injection for icon/label markup (on by default).
-   Inline SVG icons inherit the callout color and can be overridden per type.
-   Default icons are taken from the Lucide icon set.
-   Ships with two CSS themes (GitHub + Svelte inspired) that you can drop into your site immediately.
-   Built for Kirby’s KirbyText parser-no extra tooling required.

_Note_: KirbyText mixes Markdown with Kirby-specific tags. This plugin focuses on the Markdown portion while staying compatible with KirbyText’s rendering pipeline.

## Configuration

Set options in `site/config/config.php` if you need to customize behaviour:

```php
return [
    'lemmon.callouts.classPrefix' => 'callout',
    'lemmon.callouts.renderHeader' => true,
    'lemmon.callouts.icons' => [
        // Provide inline SVG (using currentColor keeps it in sync with theme accents)
        'tip' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2c4.418 0 8 3.477 8 7.77 0 2.616-1.424 4.98-3.566 6.249-.662.393-1.062 1.112-1.062 1.885V18H8.628v-.096c0-.773-.4-1.492-1.062-1.885C5.424 14.75 4 12.386 4 9.77 4 5.477 7.582 2 12 2Z"/></svg>',
        'default' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 2.5-3 5"/><circle cx="12" cy="19" r="0.5"/></svg>',
    ],
    'lemmon.callouts.wrapper' => 'div', // 'div' or 'blockquote'
];
```

-   `classPrefix`: changing this updates both wrapper classes (`{prefix} {prefix}--{type}`) and CSS selectors.
-   `renderHeader`: toggle the injected `<header>` that contains the icon span and label (themes expect this to stay on).
-   `icons`: associative array mapping modifier slugs (e.g. `note`, `tip`) to inline SVG strings. Using `currentColor` keeps icons in step with the callout accent, but you can hard-code colours if you prefer. Provide a `default` entry for fallback usage.
-   `wrapper`: switch between `<div>` (default) and `<blockquote>` depending on your semantic preference.
-   Any `[!TYPE]` yields classes like `callout callout--type` for styling hooks.

## Styling

> [!TIP]
> Bundled styles are optional; callouts expose straightforward class names (`callout`, `callout--tip`, `callout__header`, etc.), so rolling your own theme is simple. Prefer a starting point? Two drop-in themes ship with the plugin.

| Theme  | Path                         | Notes                                                       |
| ------ | ---------------------------- | ----------------------------------------------------------- |
| GitHub | `assets/callouts-github.css` | GitHub-like indicator with Lucide icons and minimal border. |
| Svelte | `assets/callouts-svelte.css` | Compact vertical glyph inspired by Svelte docs.             |

Both styles rely on CSS custom properties (accent colour, icon size). Override them in your own stylesheet to match brand guidelines:

```css
.callout--tip {
    --callout-color: #0f766e;
}
```

Custom callout types inherit the neutral defaults-just define selectors like `.callout--spotlight` if you need bespoke colors.

The injected markup looks like:

```html
<div class="callout callout--spotlight">
    <header class="callout__header" aria-label="SPOTLIGHT">
        <span class="callout__icon" aria-hidden="true"><svg></svg></span>
        <span class="callout__label">SPOTLIGHT</span>
    </header>
    <!-- KirbyText/Markdown content -->
</div>
```

> [!NOTE]
> Rendered markup follows BEM conventions: `callout` is the block, `callout--type` modifiers hook variants, and `callout__header` / `callout__icon` / `callout__label` elements keep themes predictable.

Use those modifier classes (`callout--tip`, `callout--note`, etc.) to tweak accent colours. Icons inherit `currentColor`, so adjusting `--callout-color` automatically recolours the frame (GitHub) or glyph (Svelte). Disabling `renderHeader` removes the `<header>` block in case you prefer pure CSS badges.

See `EXAMPLE.md` for a GitHub-renderable example covering every built-in type plus a custom `[!SPOTLIGHT]` callout.

## License

MIT License. See `LICENSE` for details.

---

Questions, issues, or ideas? File them in the repository or reach out-this plugin is designed to be extended.

---

Icons are based on the [Lucide](https://lucide.dev) icon set (MIT License).

## Roadmap

-   [x] Add Composer installation.
-   [ ] Add label translations support.
-   [ ] Allow custom inline labels for known types (e.g. `> [!TIP] My Fancy Label Tip`).
-   [ ] Explore Tailwind-friendly styling option.
-   [ ] Create custom block type for Blocks Field.
