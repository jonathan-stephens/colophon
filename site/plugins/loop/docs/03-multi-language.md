---
title: Multi-Language
---

Kirby Loop provides comprehensive support for multi-language Kirby sites, including automatic language detection and customizable UI translations.

## How Multi-Language Support Works

The plugin automatically detects and adapts to your Kirby site's language configuration. No additional configuration is required - the plugin works automatically with Kirby's multi-language setup.

- **Single-language sites**: Uses the en translations
- **Multi-language sites**: Detects the current page language and adapts accordingly



## UI Language Override

### Forcing a Specific UI Language

By default, loop UI adapts to the current page language. You can override this behavior:

```php
// Always show German UI regardless of page language
'moinframe.loop.language' => 'de',

// Always show English UI regardless of page language
'moinframe.loop.language' => 'en',
```

### Use Cases for Language Override

**Consistent Editor Experience:**
```php
// Editors prefer English UI even on German pages
'moinframe.loop.language' => 'en',
```

**Single-Language website with non english content:**
```php
// German content site with German-speaking editors
'moinframe.loop.language' => 'de',
```


## Built-in Translations

The plugin includes complete translations for:
- English (en) - Default
- German (de)

## Custom Translations

### Adding New Languages

To add support for additional languages, create or extend your Kirby language files:

```php
// site/languages/fr.php
<?php

return [
    'code' => 'fr',
    'default' => false,
    'direction' => 'ltr',
    'locale' => 'fr_FR',
    'name' => 'Français',
    'translations' => [
        // UI Elements
        'moinframe.loop.ui.header.title' => 'Commentaires',
        ...
    ]
];
```

### Overriding Existing Translations

Customize existing translations by adding them to your language files:

```php
// site/languages/en.php - Override English defaults
return [
    'code' => 'en',
    'default' => true,
    'translations' => [
        'moinframe.loop.ui.header.title' => 'Page Feedback',
        'moinframe.loop.ui.comment.placeholder' => 'What needs attention?',
        'moinframe.loop.ui.welcome.headline' => 'Welcome to Our Review Tool',
    ]
];
```

### Translation Key Reference

For a complete list of available translation keys, see the [plugin's index file](https://github.com/moinframe/kirby-loop/blob/main/index.php).
