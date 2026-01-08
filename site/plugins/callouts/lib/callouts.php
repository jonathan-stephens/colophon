<?php

declare(strict_types=1);

namespace Lemmon\Callouts;

use Kirby\Cms\App;
use Throwable;

/**
 * Callout renderer that transforms GitHub-style callouts into HTML callout markup.
 */
final class Renderer
{
    /**
     * Default CSS class prefix for rendered callouts.
     */
    public const DEFAULT_CLASS_PREFIX = 'callout';

    /**
     * Whether to render an HTML header inside each callout by default.
     */
    public const DEFAULT_RENDER_HEADER = true;

    /**
     * Default wrapper tag used for rendered callouts.
     */
    public const DEFAULT_WRAPPER = 'div';

    /**
     * Default inline SVG icons keyed by modifier slug.
     *
     * @var array<string, string>
     */
    public const DEFAULT_ICONS = [
        'note' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info-icon lucide-info"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
        'tip' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lightbulb-icon lucide-lightbulb"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>',
        'important' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-warning-icon lucide-message-square-warning"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/><path d="M12 15h.01"/><path d="M12 7v4"/></svg>',
        'warning' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert-icon lucide-triangle-alert"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
        'caution' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-octagon-alert-icon lucide-octagon-alert"><path d="M12 16h.01"/><path d="M12 8v4"/><path d="M15.312 2a2 2 0 0 1 1.414.586l4.688 4.688A2 2 0 0 1 22 8.688v6.624a2 2 0 0 1-.586 1.414l-4.688 4.688a2 2 0 0 1-1.414.586H8.688a2 2 0 0 1-1.414-.586l-4.688-4.688A2 2 0 0 1 2 15.312V8.688a2 2 0 0 1 .586-1.414l4.688-4.688A2 2 0 0 1 8.688 2z"/></svg>',
        'default' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-radio-tower-icon lucide-radio-tower"><path d="M4.9 16.1C1 12.2 1 5.8 4.9 1.9"/><path d="M7.8 4.7a6.14 6.14 0 0 0-.8 7.5"/><circle cx="12" cy="9" r="2"/><path d="M16.2 4.8c2 2 2.26 5.11.8 7.47"/><path d="M19.1 1.9a9.96 9.96 0 0 1 0 14.1"/><path d="M9.5 18h5"/><path d="m8 22 4-11 4 11"/></svg>',
    ];

    /**
     * Regex that matches a GitHub-style callout heading.
     */
    private const CALLOUT_HEADING_PATTERN = '/^\s{0,3}>\s*\[!([^\]]+)\]\s*(.*)$/i';

    /**
     * Regex that strips the blockquote prefix from a line.
     */
    private const BLOCKQUOTE_PREFIX_PATTERN = '/^\s{0,3}>\s?/';

    /**
     * Transforms GitHub-style callouts found inside blockquotes into HTML callout markup.
     *
     * @param string $text   Raw markdown content.
     * @param array<string, mixed> $config Runtime configuration overrides.
     */
    public static function transform(string $text, array $config = []): string
    {
        $config = self::mergeConfig($config);

        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $normalized);
        $lineCount = count($lines);

        $result = [];
        $index = 0;

        while ($index < $lineCount) {
            if (self::isBlockquoteLine($lines[$index])) {
                [$block, $index] = self::collectBlockquote($lines, $index);
                $result[] = self::renderBlock($block, $config);
                continue;
            }

            $result[] = $lines[$index];
            $index++;
        }

        return implode("\n", $result);
    }

    /**
     * Collects contiguous blockquote lines from the current cursor position.
     *
     * @param array<int, string> $lines
     * @param int $start
     *
     * @return array{0: array<int, string>, 1: int}
     */
    private static function collectBlockquote(array $lines, int $start): array
    {
        $block = [];
        $lineCount = count($lines);

        while ($start < $lineCount && self::isBlockquoteLine($lines[$start])) {
            $block[] = $lines[$start];
            $start++;
        }

        return [$block, $start];
    }

    /**
     * Renders a single blockquote block, returning either transformed callout HTML or the original block.
     *
     * @param array<int, string> $block
     * @param array{classPrefix: string, wrapper: string, renderHeader: bool, icons: array<string, string>} $config
     */
    private static function renderBlock(array $block, array $config): string
    {
        if ($block === []) {
            return '';
        }

        $headingLine = ltrim($block[0]);
        if (preg_match(self::CALLOUT_HEADING_PATTERN, $headingLine, $matches) !== 1) {
            return implode("\n", $block);
        }

        $rawType = trim($matches[1]);
        $type = strtoupper($rawType);
        $titleRemainder = trim($matches[2]);

        $contentLines = [];
        if ($titleRemainder !== '') {
            $contentLines[] = $titleRemainder;
        }

        $lineTotal = count($block);
        for ($i = 1; $i < $lineTotal; $i++) {
            $contentLines[] = self::stripBlockquotePrefix($block[$i]);
        }

        $content = trim(implode("\n", $contentLines));
        $html = self::renderContent($content);

        $meta = self::buildCalloutMeta($config['classPrefix'], $type, $rawType, $config);

        $segments = [];
        if ($config['renderHeader']) {
            $segments[] = self::renderHeader($meta);
        }

        if ($html !== '') {
            $segments[] = self::indent($html);
        }

        $content = implode("\n", array_filter($segments, static fn(string $segment): bool => $segment !== ''));

        return self::wrapContent($content, $meta, $config['wrapper']);
    }

    /**
     * Determines if the provided line belongs to a Markdown-style (KirbyText) blockquote.
     */
    private static function isBlockquoteLine(?string $line): bool
    {
        if ($line === null) {
            return false;
        }

        return preg_match('/^\s{0,3}>/', $line) === 1;
    }

    /**
     * Strips the blockquote prefix (`>`) from a given line.
     */
    private static function stripBlockquotePrefix(string $line): string
    {
        return preg_replace(self::BLOCKQUOTE_PREFIX_PATTERN, '', $line) ?? $line;
    }

    /**
     * Renders callout content to HTML, using Kirby's kirbytext helper when available.
     */
    private static function renderContent(string $content): string
    {
        if ($content === '') {
            return '';
        }

        return App::instance()->kirbytext($content);
    }

    /**
     * Indents each line in a block of HTML for readability.
     */
    private static function indent(string $html, int $level = 1): string
    {
        $indent = str_repeat('    ', $level);

        $lines = preg_split('/\R/', $html) ?: [];
        $indented = array_map(
            static fn(string $line): string => $indent . rtrim($line),
            $lines
        );

        return implode("\n", $indented);
    }

    /**
     * Merges runtime configuration with defaults.
     *
     * @param array<string, mixed> $config
     *
     * @return array{classPrefix: string, wrapper: string, renderHeader: bool, icons: array<string, string>}
     */
    private static function mergeConfig(array $config): array
    {
        $classPrefix = $config['classPrefix'] ?? self::DEFAULT_CLASS_PREFIX;

        if (!is_string($classPrefix)) {
            $classPrefix = self::DEFAULT_CLASS_PREFIX;
        }

        $classPrefix = trim($classPrefix);

        if ($classPrefix === '') {
            $classPrefix = self::DEFAULT_CLASS_PREFIX;
        }

        $renderHeader = array_key_exists('renderHeader', $config) ? (bool) $config['renderHeader'] : self::DEFAULT_RENDER_HEADER;
        $wrapper = $config['wrapper'] ?? self::DEFAULT_WRAPPER;
        if (!in_array($wrapper, ['div', 'blockquote'], true)) {
            $wrapper = self::DEFAULT_WRAPPER;
        }

        $icons = $config['icons'] ?? self::DEFAULT_ICONS;
        if (!is_array($icons)) {
            $icons = self::DEFAULT_ICONS;
        }

        return [
            'classPrefix' => $classPrefix,
            'renderHeader' => $renderHeader,
            'icons' => $icons,
            'wrapper' => $wrapper,
        ];
    }

    /**
     * Builds CSS class and metadata used for rendering the callout wrapper.
     *
     * @return array{classes: string, modifier: string, label: string, prefix: string, icon: string}
     */
    private static function buildCalloutMeta(string $classPrefix, string $type, string $rawType, array $config): array
    {
        $modifier = self::typeModifier($type);

        $baseClass = $classPrefix;
        $modifierClass = sprintf('%s--%s', $classPrefix, $modifier);

        return [
            'classes' => trim(sprintf('%s %s', $baseClass, $modifierClass)),
            'modifier' => $modifier,
            'label' => self::typeLabel($rawType),
            'prefix' => $classPrefix,
            'icon' => self::iconForModifier($modifier, $config['icons']),
        ];
    }

    /**
     * Resolves the CSS modifier for the provided callout type.
     */
    private static function typeModifier(string $type): string
    {
        $canonical = strtoupper($type);

        $slug = strtolower($canonical);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'callout';
    }

    /**
     * Wraps the rendered content using the configured tag.
     */
    private static function wrapContent(string $content, array $meta, string $wrapper): string
    {
        $classes = $meta['classes'];

        if ($content === '') {
            return sprintf('<%1$s class="%2$s"></%1$s>', $wrapper, $classes);
        }

        return sprintf(
            "<%1\$s class=\"%2\$s\">\n%3\$s\n</%1\$s>",
            $wrapper,
            $classes,
            $content
        );
    }

    /**
     * Normalizes the callout label used for visual headers.
     */
    private static function typeLabel(string $rawType): string
    {
        $normalized = trim($rawType);
        if ($normalized === '') {
            return 'CALLOUT';
        }

        $normalized = preg_replace('/[\s_]+/', ' ', $normalized) ?? $normalized;

        return strtoupper($normalized);
    }

    /**
     * Renders an optional header element containing icon and label placeholders.
     */
    private static function renderHeader(array $meta): string
    {
        $label = htmlspecialchars($meta['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $prefix = htmlspecialchars($meta['prefix'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $icon = $meta['icon'];

        $headerClass = sprintf('%s__header', $prefix);
        $iconClass = sprintf('%s__icon', $prefix);
        $labelClass = sprintf('%s__label', $prefix);

        return sprintf(
            '    <header class="%1$s" aria-label="%2$s"><span class="%3$s" aria-hidden="true">%4$s</span><span class="%5$s">%6$s</span></header>',
            $headerClass,
            $label,
            $iconClass,
            $icon,
            $labelClass,
            $label
        );
    }

    /**
     * Normalises the icon map to ensure strings and fallbacks.
     *
     * @param array<string, mixed> $icons
     *
     * @return array<string, string>
     */
    /**
     * Returns the inline SVG for the given modifier.
     *
     * @param array<string, string> $icons
     */
    private static function iconForModifier(string $modifier, array $icons): string
    {
        $key = strtolower($modifier);

        if (isset($icons[$key]) && is_string($icons[$key])) {
            return $icons[$key];
        }

        if (isset($icons['default']) && is_string($icons['default'])) {
            return $icons['default'];
        }

        return self::DEFAULT_ICONS['default'];
    }
}
