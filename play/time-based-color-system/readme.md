# Integrated Time-Based Color Token System

A sophisticated, production-ready color management system that dynamically adapts interface colors and shadows based on time of day, built with modern CSS architecture and progressive enhancement principles.

## 🌅 Overview

This system combines several cutting-edge web technologies to create a seamless, time-aware color experience:

- **OKLCH Color Space**: Perceptually uniform color modifications
- **CSS Layers**: Predictable cascade management 
- **Container Queries**: Responsive component adaptations
- **Progressive Enhancement**: Full backward compatibility
- **Dynamic Time Mapping**: 5 distinct time periods with automatic transitions
- **System Integration**: Respects user preferences and system settings

## ✨ Features

### Core Capabilities
- **🕐 Time-Based Adjustments**: 5 distinct periods (Dawn, Morning, Afternoon, Evening, Night)
- **🎨 OKLCH Color Space**: Perceptually uniform lightness modifications across all palettes
- **🎭 Dynamic Shadows**: Time-aware shadows that simulate sun position and intensity  
- **🧩 Semantic Token System**: Performance-optimized color token architecture
- **📱 Container Queries**: Size-responsive component adaptations
- **🌓 System Preference Integration**: Automatic dark/light mode mapping
- **♿ Accessibility**: High contrast mode detection and full keyboard navigation
- **🔄 Progressive Enhancement**: Works everywhere, enhanced where supported

### Technical Architecture
- **CSS Layers**: `reset → base-palette → semantic-tokens → time-adjustments → components → utilities`
- **Fallback Strategy**: OKLCH → color-mix → HSL with graceful degradation
- **Performance**: Efficient CSS custom property updates, minimal JavaScript
- **Type Safety**: CSS `@property` declarations for enhanced debugging

## 🚀 Quick Start

### Basic Implementation

1. **Include the System**: Copy the HTML file or extract the CSS/JS components
2. **Initialize**: The system auto-initializes on `DOMContentLoaded`
3. **Use Semantic Tokens**: Apply color tokens to your components

```css
.my-component {
    background: var(--token-surface);
    color: var(--token-foreground);
    border: 1px solid var(--token-border);
    box-shadow: var(--shadow-current);
}
```

### Color Palettes

Apply palette classes to use different color schemes:

```html
<div class="card palette-secondary">Secondary palette</div>
<div class="card palette-tertiary">Tertiary palette</div>
<button class="btn palette-quaternary">Quaternary button</button>
```

## 🎨 Color System Architecture

### Base Palette Structure

Each palette contains 7 lightness levels:
- `--color-darkest` (15-25% lightness)
- `--color-darker` (25-35% lightness) 
- `--color-dark` (35-45% lightness)
- `--color-core` (45-65% lightness) - Primary brand color
- `--color-light` (60-75% lightness)
- `--color-lighter` (75-85% lightness)
- `--color-lightest` (85-95% lightness)

### Semantic Token Mapping

Semantic tokens provide consistent interface mapping:

```css
--token-background    /* Page/container backgrounds */
--token-surface       /* Card/component surfaces */
--token-hover-surface /* Interactive hover states */
--token-active-background /* Active/pressed states */
--token-foreground    /* Primary text color */
--token-border        /* Border and divider colors */
--token-frill         /* Decorative/accent elements */
```

### Time Period Adjustments

| Period | Hours | Lightness Shift | Shadow Intensity | Theme Preference |
|--------|-------|-----------------|------------------|------------------|
| Dawn | 5AM-9AM | +10-15% | 30% | Light |
| Morning | 9AM-1PM | Base values | 20% | Light |
| Afternoon | 1PM-5PM | -5% (peak brightness) | 15% | Light |
| Evening | 5PM-9PM | +10-15% | 40% | Dark |
| Night | 9PM-5AM | +5-10% | 60% | Dark |

## 📐 CSS Layers Explained

The system uses CSS Layers for predictable cascade control:

```css
@layer reset, base-palette, semantic-tokens, time-adjustments, components, utilities;
```

### Layer Purposes

1. **`reset`**: Basic element resets and box-sizing
2. **`base-palette`**: Static OKLCH color definitions and fallbacks
3. **`semantic-tokens`**: Token mapping and theme-based overrides  
4. **`time-adjustments`**: Dynamic time-period modifications
5. **`components`**: Component styles using semantic tokens
6. **`utilities`**: Debug tools and utility classes

This architecture ensures time adjustments always override base colors, while utilities can override everything.

## 🔧 JavaScript API

### Core Methods

```javascript
// Access the global instance
const colorSystem = window.colorSystem;

// Manual time period control
colorSystem.updateTimeBasedColors('evening'); // Force specific period
colorSystem.isAuto = true; // Re-enable auto mode

// Get current state
console.log(colorSystem.currentPeriod); // Current time period
console.log(colorSystem.isAuto); // Auto mode status
console.log(colorSystem.currentTheme); // Manual theme override
```

### Configuration Object

```javascript
colorSystem.timePeriods = {
    dawn: { 
        start: 5, end: 9, 
        description: 'Soft morning lighting with gentle contrast',
        sunAngle: 15,
        shadowIntensity: 0.3,
        preferredTheme: 'light'
    }
    // ... other periods
};
```

## 🎯 Usage Examples

### Basic Component

```html
<div class="card">
    <h3>My Component</h3>
    <p>This automatically adapts to time periods</p>
    <button class="btn">Action</button>
</div>
```

### Multi-Palette Interface

```html
<nav class="palette-primary">
    <button class="btn">Primary Action</button>
</nav>

<main class="palette-secondary">
    <article class="card">Content</article>
</main>

<aside class="palette-tertiary">
    <div class="card">Sidebar</div>
</aside>
```

### Custom Component with Time Awareness

```css
.my-custom-component {
    background: var(--token-surface);
    border: 2px solid var(--token-border);
    color: var(--token-foreground);
    box-shadow: var(--shadow-current);
    transition: all 0.3s ease;
}

.my-custom-component:hover {
    background: var(--token-hover-surface);
    transform: translateY(-2px);
}

/* Time-specific customizations */
.time-night .my-custom-component {
    /* Additional night-specific styles */
    backdrop-filter: blur(8px);
}
```

## 🔨 Extending the System

### Adding New Color Palettes

1. **Define base colors in the `base-palette` layer:**

```css
@layer base-palette {
    :root {
        --senary-hue: 120;
        --senary-chroma: 0.18;
        --senary-fallback: hsl(120, 60%, 50%);
    }
    
    .palette-senary {
        --color-darkest: oklch(var(--lightness-darkest) var(--senary-chroma) var(--senary-hue));
        --color-darker: oklch(var(--lightness-darker) var(--senary-chroma) var(--senary-hue));
        /* ... continue for all 7 levels */
    }
}
```

2. **Add fallback support:**

```css
@supports not (color: oklch(50% 0.1 180)) {
    .palette-senary {
        --color-core: var(--senary-fallback);
        --color-darkest: color-mix(in srgb, var(--color-core) 20%, black);
        /* ... continue for all levels */
    }
}
```

### Custom Time Periods

```javascript
// Add a new time period
colorSystem.timePeriods.twilight = {
    start: 19, end: 21,
    description: 'Magical blue hour',
    sunAngle: 5,
    shadowIntensity: 0.5,
    preferredTheme: 'dark'
};

// Create corresponding CSS class
```

```css
@layer time-adjustments {
    .time-twilight {
        --lightness-darkest: 20%;
        --lightness-darker: 30%;
        /* ... custom lightness values */
        --shadow-current: var(--shadow-twilight);
        
        /* Custom token overrides */
        --token-frill: color-mix(in oklch, var(--color-core) 70%, blue);
    }
}
```

### Adding New Semantic Tokens

```css
@layer semantic-tokens {
    :root, .palette-primary, /* ... all palettes */ {
        --token-success: var(--color-light);
        --token-warning: var(--color-core);
        --token-error: oklch(var(--lightness-core) 0.2 15);
        --token-info: oklch(var(--lightness-core) 0.15 240);
    }
    
    /* Theme-specific overrides */
    @media (prefers-color-scheme: dark) {
        :root, .palette-primary, /* ... all palettes */ {
            --token-success: var(--color-dark);
            --token-warning: var(--color-light);
        }
    }
}
```

## ⚡ Performance Considerations

### Optimization Strategies

1. **CSS Custom Properties**: Efficient updates without style recalculation
2. **Minimal JavaScript**: Updates only when necessary (hourly for auto mode)
3. **Progressive Enhancement**: Base functionality works without JavaScript
4. **Container Queries**: Efficient responsive behavior without media queries
5. **Layer Architecture**: Optimized cascade with minimal specificity conflicts

### Performance Monitoring

```javascript
// Monitor color system performance
const observer = new PerformanceObserver((list) => {
    list.getEntries().forEach((entry) => {
        if (entry.name.includes('color-system')) {
            console.log(`${entry.name}: ${entry.duration}ms`);
        }
    });
});
observer.observe({ entryTypes: ['measure'] });

// Measure time period updates
performance.mark('color-system-update-start');
colorSystem.updateTimeBasedColors('evening');
performance.mark('color-system-update-end');
performance.measure('color-system-update', 'color-system-update-start', 'color-system-update-end');
```

## ♿ Accessibility Features

### Built-in Accessibility

- **System Preference Respect**: Honors `prefers-color-scheme` and `prefers-contrast`
- **High Contrast Mode**: Automatic detection with visual indicator
- **Color Independence**: All states have sufficient contrast ratios
- **Keyboard Navigation**: Full keyboard support for all controls
- **Reduced Motion**: Respects `prefers-reduced-motion` (add if needed)

### WCAG Compliance

The system maintains WCAG 2.1 AA compliance:
- **Contrast Ratios**: All text maintains 4.5:1 minimum contrast
- **Color Semantics**: Information isn't conveyed by color alone
- **Focus Indicators**: Clear focus states on all interactive elements

### Adding Reduced Motion Support

```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
    }
}
```

## 🌐 Browser Support

### Full Support (Modern Browsers)
- **Chrome 111+**: Complete OKLCH and container query support
- **Firefox 113+**: Full feature support  
- **Safari 16.4+**: Complete implementation

### Graceful Degradation
- **Chrome 88+**: Color-mix fallbacks, no container queries
- **Firefox 88+**: HSL fallbacks with limited features
- **Safari 14+**: Basic functionality with HSL colors
- **IE 11**: Core functionality only (requires additional polyfills)

### Feature Detection

The system automatically detects and adapts to browser capabilities:

```css
/* OKLCH support detection */
@supports (color: oklch(50% 0.1 180)) {
    /* Enhanced OKLCH implementation */
}

/* Color-mix fallback */
@supports not (color: oklch(50% 0.1 180)) {
    /* color-mix fallback implementation */
}

/* Container query detection */
@supports (container-type: inline-size) {
    /* Enhanced responsive behavior */
}
```

## 🐛 Debugging and Troubleshooting

### Debug Mode

Enable debug logging by setting:

```javascript
colorSystem.debugMode = true;
```

### Common Issues

1. **Colors not updating**: Check if JavaScript is enabled and console for errors
2. **OKLCH not supported**: System should automatically fall back to color-mix or HSL
3. **Time periods not switching**: Verify system clock and time zone settings
4. **Performance issues**: Check for excessive DOM updates during transitions

### Debug Utilities

```css
@layer utilities {
    /* Visualize current time period */
    .debug-time::before {
        content: attr(class);
        position: fixed;
        top: 0;
        left: 0;
        background: var(--token-surface);
        color: var(--token-foreground);
        padding: 0.5rem;
        font-family: monospace;
        z-index: 9999;
    }
    
    /* Show current color values */
    .debug-colors {
        background: linear-gradient(
            45deg,
            var(--color-darkest) 0%,
            var(--color-core) 50%,
            var(--color-lightest) 100%
        );
    }
}
```

## 📄 License and Credits

### Design Philosophy

This system is built on the principle that interfaces should feel natural and respond to environmental context, just as our eyes adapt to different lighting conditions throughout the day.

### Inspiration and Credits

- **OKLCH Color Space**: Based on research by Björn Ottosson
- **CSS Layers**: Implementation follows CSS Cascade Layers Level 1 specification
- **Container Queries**: Follows CSS Containment Module Level 3
- **Time-Based Design**: Inspired by circadian rhythm research and natural lighting patterns

### Contributing

To contribute to this system:
1. Follow the established CSS layer architecture
2. Maintain backward compatibility
3. Test across supported browsers
4. Update documentation for new features
5. Ensure accessibility compliance

---

**Version**: 1.0.0  
**Last Updated**: August 2025  
**Compatibility**: Modern browsers with progressive enhancement