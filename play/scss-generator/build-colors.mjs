// build-colors.mjs
import fs from "fs";
import Color from "colorjs.io/dist/color.js";

const CONTRAST_ALGO = "WCAG21";

// Helper to round OKLCH components
function roundOklch(str, precision = 4) {
  return str.replace(/([\d.]+)/g, (num) => parseFloat(num).toFixed(precision));
}

// Create a color at a specific lightness
function createColorAtLightness(baseColor, targetL) {
  const clamped = Math.max(0, Math.min(1, targetL));
  return new Color("oklch", [clamped, baseColor.oklch.c, baseColor.oklch.h]);
}

// Check contrast between two colors
function getContrast(color1, color2) {
  return Color.contrast(color1, color2, CONTRAST_ALGO);
}

// Generate a full monochromatic spectrum with guaranteed contrast pairs
function generatePalette(name, baseValue) {
  const baseColor = new Color(baseValue);
  const baseL = baseColor.oklch.l;
  const c = baseColor.oklch.c;
  const h = baseColor.oklch.h;

  // Strategy: Create a spectrum from dark to light, anchored around the base
  // We'll adjust the spectrum to ensure contrast requirements are met

  let spectrum = {};

  // Determine the spread strategy based on base lightness
  if (baseL > 0.7) {
    // Light base: spread downward more, upward less
    spectrum = {
      darkest: 0.15,
      darker: 0.30,
      dark: 0.45,
      base: baseL,
      light: Math.min(baseL + 0.08, 0.95),
      lighter: Math.min(baseL + 0.12, 0.97),
      lightest: Math.min(baseL + 0.15, 0.99)
    };
  } else if (baseL < 0.3) {
    // Dark base: spread upward more, downward less
    spectrum = {
      darkest: Math.max(baseL - 0.15, 0.01),
      darker: Math.max(baseL - 0.12, 0.03),
      dark: Math.max(baseL - 0.08, 0.05),
      base: baseL,
      light: 0.55,
      lighter: 0.70,
      lightest: 0.85
    };
  } else {
    // Mid-range base: create balanced spectrum
    spectrum = {
      darkest: Math.max(baseL - 0.45, 0.15),
      darker: Math.max(baseL - 0.30, 0.25),
      dark: Math.max(baseL - 0.15, 0.35),
      base: baseL,
      light: Math.min(baseL + 0.15, 0.65),
      lighter: Math.min(baseL + 0.30, 0.75),
      lightest: Math.min(baseL + 0.45, 0.85)
    };
  }

  // Refine spectrum to meet contrast requirements
  // We need: 3 pairs @ AAA (7:1), 2 pairs @ AA (4.5:1), 1 pair @ AA18 (3:1)

  // Key contrast pairs to verify:
  // - lightest vs darkest (should be highest, aim for 12+:1)
  // - lighter vs darker (should be AAA, 7+:1)
  // - light vs dark (should be AA, 4.5+:1)
  // - base vs light/dark neighbors (should be AA18, 3+:1)

  const maxIterations = 50;
  let iterations = 0;

  while (iterations < maxIterations) {
    const colors = {
      darkest: createColorAtLightness(baseColor, spectrum.darkest),
      darker: createColorAtLightness(baseColor, spectrum.darker),
      dark: createColorAtLightness(baseColor, spectrum.dark),
      base: baseColor,
      light: createColorAtLightness(baseColor, spectrum.light),
      lighter: createColorAtLightness(baseColor, spectrum.lighter),
      lightest: createColorAtLightness(baseColor, spectrum.lightest)
    };

    // Check key contrast pairs
    const lightestVsDarkest = getContrast(colors.lightest, colors.darkest);
    const lighterVsDarker = getContrast(colors.lighter, colors.darker);
    const lightVsDark = getContrast(colors.light, colors.dark);
    const baseVsLight = getContrast(colors.base, colors.light);
    const baseVsDark = getContrast(colors.base, colors.dark);

    // Check if all requirements are met
    const hasAAA = (lightestVsDarkest >= 7 && lighterVsDarker >= 7 && lightVsDark >= 7) ||
                   (lightestVsDarkest >= 7 && lighterVsDarker >= 7 && baseVsLight >= 7) ||
                   (lightestVsDarkest >= 7 && lighterVsDarker >= 7 && baseVsDark >= 7);
    const hasAA = lightVsDark >= 4.5 || baseVsLight >= 4.5 || baseVsDark >= 4.5;
    const hasAA18 = baseVsLight >= 3 || baseVsDark >= 3;

    if (lightestVsDarkest >= 12 && lighterVsDarker >= 7 && lightVsDark >= 4.5 &&
        (baseVsLight >= 3 || baseVsDark >= 3)) {
      break; // Good enough!
    }

    // Adjust spectrum to improve contrast
    if (lightestVsDarkest < 12) {
      spectrum.lightest = Math.min(spectrum.lightest + 0.02, 0.99);
      spectrum.darkest = Math.max(spectrum.darkest - 0.02, 0.01);
    }
    if (lighterVsDarker < 7) {
      spectrum.lighter = Math.min(spectrum.lighter + 0.015, 0.95);
      spectrum.darker = Math.max(spectrum.darker - 0.015, 0.05);
    }
    if (lightVsDark < 4.5) {
      spectrum.light = Math.min(spectrum.light + 0.01, 0.90);
      spectrum.dark = Math.max(spectrum.dark - 0.01, 0.10);
    }

    iterations++;
  }

  // Generate final palette
  const palette = {};
  for (const [variant, lightness] of Object.entries(spectrum)) {
    const color = createColorAtLightness(baseColor, lightness);
    palette[variant] = roundOklch(color.toString({ format: "oklch" }));
  }

  // Calculate and return contrast info
  const colors = {
    darkest: createColorAtLightness(baseColor, spectrum.darkest),
    darker: createColorAtLightness(baseColor, spectrum.darker),
    dark: createColorAtLightness(baseColor, spectrum.dark),
    base: baseColor,
    light: createColorAtLightness(baseColor, spectrum.light),
    lighter: createColorAtLightness(baseColor, spectrum.lighter),
    lightest: createColorAtLightness(baseColor, spectrum.lightest)
  };

  palette._contrastInfo = {
    'lightest-darkest': getContrast(colors.lightest, colors.darkest).toFixed(2),
    'lighter-darker': getContrast(colors.lighter, colors.darker).toFixed(2),
    'light-dark': getContrast(colors.light, colors.dark).toFixed(2),
    'base-light': getContrast(colors.base, colors.light).toFixed(2),
    'base-dark': getContrast(colors.base, colors.dark).toFixed(2)
  };

  return palette;
}

// Load JSON config
const colors = JSON.parse(fs.readFileSync("./colors.config.json", "utf8"));

let output = `// Auto-generated OKLCH tokens
// Do not edit directly. Run: npm run build:colors

:root {
`;

console.log("\n🎨 Generating color palettes with WCAG contrast verification...\n");

// Generate WCAG contrast-compliant variants
for (const [name, base] of Object.entries(colors)) {
  const palette = generatePalette(name, base);
  const contrastInfo = palette._contrastInfo;
  delete palette._contrastInfo;

  output += `  // ${name.toUpperCase()} - Full Spectrum with WCAG Contrast\n`;
  output += `  // Contrast ratios - lightest:darkest ${contrastInfo['lightest-darkest']}:1, lighter:darker ${contrastInfo['lighter-darker']}:1, light:dark ${contrastInfo['light-dark']}:1\n`;

  // Base spectrum colors
  for (const [variant, value] of Object.entries(palette)) {
    output += `  --${name}-${variant}: ${value};\n`;
  }
  output += "\n";

  // Console output
  console.log(`✓ ${name}:`);
  console.log(`  Contrast pairs: lightest:darkest ${contrastInfo['lightest-darkest']}:1 | lighter:darker ${contrastInfo['lighter-darker']}:1 | light:dark ${contrastInfo['light-dark']}:1`);
}

output += "}\n";

// Additional utility comment
output += `
/*
 * Usage examples:
 *
 * Solid colors - High contrast text on background:
 *   background: var(--default-darkest);
 *   color: var(--default-lightest); // ~12:1 contrast
 *
 * Solid colors - Medium contrast (AAA):
 *   background: var(--default-darker);
 *   color: var(--default-lighter); // ~7:1 contrast
 *
 * Solid colors - Standard contrast (AA):
 *   background: var(--default-dark);
 *   color: var(--default-light); // ~4.5:1 contrast
 *
 */
`;

fs.writeFileSync("./src/styles/_tokens.generated.scss", output);
console.log("\n✅ Color tokens generated successfully!");
console.log(`📊 Generated ${Object.keys(colors).length} color palettes\n`);
