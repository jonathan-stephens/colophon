---
title: Theming
---

Kirby Loop comes with built-in theming support, allowing you to customize the visual appearance to match your brand or provide different user experiences. The plugin includes a default (light) theme and a dark theme, with support for creating custom themes.

## Configuration

### Setting a Theme

Configure the theme in your `site/config/config.php`:

```php
return [
    // Set theme: 'default', 'dark', or custom theme name
    'moinframe.loop.theme' => 'dark',
];
```

**Available options:**
- `'default'` - Light theme (default)
- `'dark'` - Dark theme
- Custom theme name

## Creating Custom Themes

Custom themes are CSS files that override the default color and styling tokens. The theming system uses CSS custom properties (variables) for easy customization.


### Basic Custom Theme

Here's a minimal custom theme example:

```css
/* frontend/src/styles/theme-custom.css */
kirby-loop[theme="custom"] {
  /* Accent color */
  --color-accent-l: 0.6;
  --color-accent-c: 0.15;
  --color-accent-h: 280; /* Purple accent */

  /* Neutral color lightness values */
  --color-neutral-l-0: 0.98;
  --color-neutral-l-100: 0.92;
  --color-neutral-l-200: 0.86;
  --color-neutral-l-300: 0.7;
  --color-neutral-l-400: 0.6;
  --color-neutral-l-500: 0.5;
  --color-neutral-l-600: 0.4;
  --color-neutral-l-700: 0.3;
  --color-neutral-l-800: 0.15;
  --color-neutral-l-900: 0.05;
  --color-neutral-l-1000: 0;
}
```

### Configure Your Custom Theme

Set your custom theme in the configuration:

```php
// site/config/config.php
return [
    'moinframe.loop.theme' => 'custom',
];
```

## Theme Architecture

### Color System

The theming system uses OKLCH color space for consistent, perceptually uniform colors:

```css
/* Accent colors */
--color-accent-l: 0.7;        /* Lightness (0-1) */
--color-accent-c: 0.12;       /* Chroma/saturation (0-0.4) */
--color-accent-h: 220;        /* Hue (0-360) */

/* Neutral colors */
--color-neutral-l-0: 1;       /* Lightest */
--color-neutral-l-100: 0.95;
--color-neutral-l-200: 0.9;
/* ... */
--color-neutral-l-900: 0.05;
--color-neutral-l-1000: 0;    /* Darkest */
```

### Advanced Customization

You can override any design token in your custom theme:

```css
kirby-loop[theme="custom"] {
  /* Colors */
  --color-accent-l: 0.65;
  --color-accent-c: 0.18;
  --color-accent-h: 15; /* Orange accent */

  /* Shadows with custom opacity */
  --shadow-s: 0 0.1em 0.25em oklch(var(--color-neutral-l-900) var(--color-neutral-c) var(--color-neutral-h) / 0.15);

  /* Custom border radius */
  --border-radius: 0.5rem;
  --border-radius-rounded: 1rem;

  /* Custom fonts */
  --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
```
