// build-colors.mjs (Enhanced: adaptive chroma, smooth steps, hue harmony, edge cases)
import fs from "fs";
import Color from "colorjs.io/dist/color.js";

const CONTRAST_ALGO = "WCAG21";
const clamp = (n, min, max) => Math.min(Math.max(n, min), max);
const roundOklch = (str, precision = 4) =>
  str.replace(/([\d.]+)/g, (num) => parseFloat(num).toFixed(precision));

const toOklch = (c) => c.toString({ format: "oklch" });
const toHex = (c) => c.to("srgb").toString({ format: "hex" });
const toRgb = (c) => {
  const [r, g, b] = c.to("srgb").coords.map((v) =>
    Math.round(clamp(v, 0, 1) * 255)
  );
  return `rgb(${r}, ${g}, ${b})`;
};
const createColorOKLCH = (l, c, h) => new Color("oklch", [l, c, h]);
const getContrast = (c1, c2) => Color.contrast(c1, c2, CONTRAST_ALGO);

// === Gamut checking ===
function isInGamut(color) {
  try {
    const srgb = color.to("srgb");
    return srgb.inGamut();
  } catch {
    return false;
  }
}

function gamutClipOKLCH(l, c, h, maxAttempts = 20) {
  let currentC = c;
  let color = createColorOKLCH(l, currentC, h);

  let attempts = 0;
  while (!isInGamut(color) && attempts < maxAttempts && currentC > 0.001) {
    currentC *= 0.95; // Reduce chroma by 5% each iteration
    color = createColorOKLCH(l, currentC, h);
    attempts++;
  }

  return { color, clipped: attempts > 0, finalChroma: currentC };
}

// === Hue/chroma utilities ===
function getMaxChromaForHue(h, l) {
  const hueNorm = ((h || 0) % 360) / 360;
  const hueFactor = Math.sin((hueNorm - 0.167) * Math.PI * 2) * 0.3 + 1;
  const lightnessFactor = 1 - Math.pow(Math.abs(l - 0.5) * 2, 1.2);
  return 0.37 * hueFactor * lightnessFactor;
}

// === Enhanced: Hue harmony adjustments based on color theory ===
function getHueShift(baseHue, targetL, baseL, dirSign) {
  const h = ((baseHue || 0) % 360 + 360) % 360;

  // Determine color family
  const isRed = (h >= 0 && h < 30) || h >= 330;
  const isOrange = h >= 30 && h < 60;
  const isYellow = h >= 60 && h < 90;
  const isGreen = h >= 90 && h < 150;
  const isCyan = h >= 150 && h < 210;
  const isBlue = h >= 210 && h < 270;
  const isPurple = h >= 270 && h < 330;

  const dist = Math.abs(targetL - baseL);
  let hueRotation = 0;

  // Enhanced hue shifts for natural color progression
  if (dirSign < 0) {
    // Going darker - shift toward richer, deeper tones
    if (isBlue || isCyan) {
      hueRotation = -8 * Math.pow(dist, 1.1); // Blues go more purple/navy
    } else if (isGreen) {
      hueRotation = 5 * Math.pow(dist, 1.1); // Greens go more teal
    } else if (isYellow || isOrange) {
      hueRotation = -6 * Math.pow(dist, 1.1); // Yellows/oranges go more amber
    } else if (isRed) {
      hueRotation = 4 * Math.pow(dist, 1.1); // Reds go more crimson
    } else if (isPurple) {
      hueRotation = -5 * Math.pow(dist, 1.1); // Purples go more violet
    }
  } else {
    // Going lighter - shift toward brighter, airier tones
    if (isBlue) {
      hueRotation = 6 * Math.pow(dist, 1.1); // Blues go more cyan/sky
    } else if (isGreen) {
      hueRotation = -4 * Math.pow(dist, 1.1); // Greens go more lime
    } else if (isYellow) {
      hueRotation = 3 * Math.pow(dist, 1.1); // Yellows stay warm
    } else if (isRed || isOrange) {
      hueRotation = 5 * Math.pow(dist, 1.1); // Reds/oranges go more coral
    } else if (isPurple) {
      hueRotation = 8 * Math.pow(dist, 1.1); // Purples go more magenta
    }
  }

  return hueRotation;
}

// === Enhanced: Pair-aware adaptive chroma ===
function adaptChromaHue(baseColor, targetL, baseL, variant, params) {
  const { c: bC, h: bH } = baseColor.oklch;
  const dist = Math.abs(targetL - baseL);
  const dirSign = targetL < baseL ? -1 : 1;

  // Enhanced: Check if truly achromatic (gray)
  const isAchromatic = bC < 0.005;
  const isNearGray = bC < 0.02 && !isAchromatic;

  // Handle pure grays specially
  if (isAchromatic) {
    return { c: 0.008, h: bH || 0 }; // Minimal chroma, preserve hue hint
  }

  const targetDistFromMid = Math.abs(targetL - 0.5);

  // ENHANCEMENT 1: Pair-aware chroma strategy
  let chromaFactor;
  if (variant === 'darkest' || variant === 'lightest') {
    // Extremes: allow more desaturation for elegance
    chromaFactor = 0.7 + 0.2 * (1 - targetDistFromMid);
  } else if (variant === 'darker' || variant === 'lighter') {
    // Workhorses: keep highly saturated
    chromaFactor = Math.max(0.9, 1 - targetDistFromMid * 0.15);
  } else if (variant === 'dark' || variant === 'light' || variant === 'mid') {
    // Brand core: maximum saturation
    chromaFactor = Math.max(0.95, 1 - targetDistFromMid * 0.1);
  } else {
    // Fallback
    chromaFactor = 1 - targetDistFromMid * 0.3;
  }

  // Adjust for base color characteristics
  if (isNearGray) {
    chromaFactor *= 1.8; // Boost near-grays more aggressively
  } else if (baseL > 0.85) {
    chromaFactor = Math.max(chromaFactor, 0.85);
  } else if (baseL < 0.20) {
    chromaFactor = Math.max(chromaFactor, 0.82);
  }

  let c = bC * chromaFactor;

  // Set minimum chroma based on variant role
  let minChroma;
  if (isNearGray) {
    minChroma = 0.018;
  } else if (variant === 'darker' || variant === 'lighter') {
    minChroma = 0.03; // Workhorses need visible color
  } else if (variant === 'mid' || variant === 'dark' || variant === 'light') {
    minChroma = 0.028; // Brand core needs strong color
  } else {
    minChroma = 0.02; // Extremes can be more subtle
  }

  c = clamp(c, minChroma, getMaxChromaForHue(bH, targetL));

  // ENHANCEMENT 3: Hue harmony based on color theory
  const harmonicShift = getHueShift(bH, targetL, baseL, dirSign);
  const h = ((bH + harmonicShift) % 360 + 360) % 360;

  return { c, h };
}

// === Cross-spectrum contrast pairs ===
function generateContrastPairs(baseL) {
  let midL = baseL;

  // Adjust mid if too extreme
  if (baseL < 0.30) {
    midL = Math.max(baseL, 0.32);
  } else if (baseL > 0.80) {
    midL = Math.min(baseL, 0.78);
  }

  // Establish outer pair for AAA contrast
  let darkestL = 0.15;
  let lightestL = 0.92;

  if (midL < 0.40) {
    darkestL = Math.max(0.10, midL - 0.25);
    lightestL = 0.94;
  } else if (midL > 0.70) {
    darkestL = 0.12;
    lightestL = Math.min(0.95, midL + 0.25);
  } else {
    darkestL = Math.max(0.12, midL - 0.35);
    lightestL = Math.min(0.93, midL + 0.35);
  }

  const darkRange = midL - darkestL;
  const lightRange = lightestL - midL;

  // Position pairs for functional contrast
  const darkL = clamp(midL - darkRange * 0.35, darkestL + 0.10, midL - 0.08);
  const lightL = clamp(midL + lightRange * 0.35, midL + 0.08, lightestL - 0.10);

  const darkerL = clamp(midL - darkRange * 0.70, darkestL + 0.06, darkL - 0.06);
  const lighterL = clamp(midL + lightRange * 0.70, lightL + 0.06, lightestL - 0.06);

  return {
    darkest: darkestL,
    darker: darkerL,
    dark: darkL,
    mid: midL,
    light: lightL,
    lighter: lighterL,
    lightest: lightestL
  };
}

// === ENHANCEMENT 2: Perceptual smoothness check ===
function ensurePerceptualSmoothness(colorsObj) {
  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const minStep = 0.04;
  const maxStep = 0.20;

  let adjusted = false;

  for (let i = 1; i < order.length; i++) {
    const prev = colorsObj[order[i - 1]];
    const curr = colorsObj[order[i]];
    const step = curr.oklch.l - prev.oklch.l;

    // Step too small - push current up
    if (step < minStep) {
      const newL = clamp(prev.oklch.l + minStep, 0.08, 0.97);
      colorsObj[order[i]] = createColorOKLCH(newL, curr.oklch.c, curr.oklch.h);
      adjusted = true;
    }

    // Step too large - insert intermediate adjustment
    if (step > maxStep && i > 1) {
      const newL = prev.oklch.l + (step * 0.6);
      colorsObj[order[i]] = createColorOKLCH(newL, curr.oklch.c, curr.oklch.h);
      adjusted = true;
    }
  }

  return { colorsObj, adjusted };
}

// === Enforce contrast pairs ===
function enforceContrastPairs(colorsObj, contrastTargets) {
  let attempts = 0;
  while (getContrast(colorsObj.darkest, colorsObj.lightest) < contrastTargets.AAA && attempts < 25) {
    const darkL = Math.max(0.08, colorsObj.darkest.oklch.l - 0.02);
    const lightL = Math.min(0.97, colorsObj.lightest.oklch.l + 0.02);

    colorsObj.darkest = createColorOKLCH(darkL, colorsObj.darkest.oklch.c, colorsObj.darkest.oklch.h);
    colorsObj.lightest = createColorOKLCH(lightL, colorsObj.lightest.oklch.c, colorsObj.lightest.oklch.h);
    attempts++;
  }

  attempts = 0;
  while (getContrast(colorsObj.darker, colorsObj.lighter) < contrastTargets.AA && attempts < 20) {
    const darkL = Math.max(0.10, colorsObj.darker.oklch.l - 0.015);
    const lightL = Math.min(0.95, colorsObj.lighter.oklch.l + 0.015);

    colorsObj.darker = createColorOKLCH(darkL, colorsObj.darker.oklch.c, colorsObj.darker.oklch.h);
    colorsObj.lighter = createColorOKLCH(lightL, colorsObj.lighter.oklch.c, colorsObj.lighter.oklch.h);
    attempts++;
  }

  attempts = 0;
  while (getContrast(colorsObj.dark, colorsObj.light) < 3 && attempts < 15) {
    const darkL = Math.max(0.15, colorsObj.dark.oklch.l - 0.01);
    const lightL = Math.min(0.90, colorsObj.light.oklch.l + 0.01);

    colorsObj.dark = createColorOKLCH(darkL, colorsObj.dark.oklch.c, colorsObj.dark.oklch.h);
    colorsObj.light = createColorOKLCH(lightL, colorsObj.light.oklch.c, colorsObj.light.oklch.h);
    attempts++;
  }

  return colorsObj;
}

// === Main palette generation ===
function generatePalette(name, rawEntry, cfg) {
  const entry = typeof rawEntry === "string" ? { base: rawEntry } : { ...rawEntry };
  const baseColor = new Color(entry.base);
  const baseL = baseColor.oklch.l;
  const baseC = baseColor.oklch.c;
  const { contrastTargets } = cfg.settings;

  // ENHANCEMENT 4: Edge case detection
  const warnings = [];
  const edgeCases = [];

  if (baseC < 0.005) {
    edgeCases.push("⚪ Achromatic input (pure gray) - adding minimal chroma");
  } else if (baseC < 0.02) {
    edgeCases.push("🌫️  Near-gray input - boosting saturation");
  }

  if (baseC > 0.30) {
    edgeCases.push("🎨 Highly saturated input - may clip at extremes");
  }

  if (baseL < 0.20) {
    edgeCases.push("🌑 Very dark base - mid adjusted upward for range");
  } else if (baseL > 0.85) {
    edgeCases.push("☀️  Very light base - mid adjusted downward for range");
  }

  // Generate contrast pair scale
  const scale = generateContrastPairs(baseL);

  // Generate colors with enhanced chroma/hue adaptation
  const colorsObj = {};
  const gamutIssues = [];

  for (const [variant, targetL] of Object.entries(scale)) {
    const { c, h } = adaptChromaHue(baseColor, targetL, scale.mid, variant, cfg.settings);

    // ENHANCEMENT 6: Gamut clipping detection and handling
    const result = gamutClipOKLCH(targetL, c, h);
    colorsObj[variant] = result.color;

    if (result.clipped) {
      gamutIssues.push(`${variant}: chroma reduced ${c.toFixed(3)}→${result.finalChroma.toFixed(3)}`);
    }
  }

  // Enforce contrast pair requirements
  let constrained = enforceContrastPairs(colorsObj, contrastTargets);

  // ENHANCEMENT 2: Ensure perceptual smoothness
  const smoothResult = ensurePerceptualSmoothness(constrained);
  constrained = smoothResult.colorsObj;

  if (smoothResult.adjusted) {
    edgeCases.push("🔧 Smoothness adjustments applied");
  }

  // Build output palette
  const palette = {};
  const variantOrder = ["darkest", "darker", "dark", "mid", "light", "lighter", "lightest"];

  for (const variant of variantOrder) {
    const col = constrained[variant];
    palette[variant] = {
      oklch: roundOklch(toOklch(col)),
      hex: toHex(col),
      rgb: toRgb(col),
    };
  }

  palette.base = {
    oklch: roundOklch(toOklch(baseColor)),
    hex: toHex(baseColor),
    rgb: toRgb(baseColor),
  };

  // Contrast pair analysis
  const { darkest, darker, dark, mid, light, lighter, lightest } = constrained;

  const pair1 = getContrast(darkest, lightest).toFixed(2);
  const pair2 = getContrast(darker, lighter).toFixed(2);
  const pair3 = getContrast(dark, light).toFixed(2);
  const midDark = getContrast(mid, darkest).toFixed(2);
  const midLight = getContrast(mid, lightest).toFixed(2);

  if (pair1 < 7) warnings.push(`❌ darkest↔lightest AAA not met (${pair1})`);
  if (pair2 < 4.5) warnings.push(`⚠️ darker↔lighter AA not met (${pair2})`);
  if (pair3 < 3) warnings.push(`⚠️ dark↔light AA-large not met (${pair3})`);

  const info = [];
  info.push(`Pairs: darkest↔lightest=${pair1}, darker↔lighter=${pair2}, dark↔light=${pair3}`);
  info.push(`Mid: ↔darkest=${midDark}, ↔lightest=${midLight}`);

  if (gamutIssues.length > 0) {
    info.push(`Gamut clipping: ${gamutIssues.join(', ')}`);
  }

  return { name, palette, baseL, warnings, info, edgeCases };
}

// ==========================================================
const colors = JSON.parse(fs.readFileSync("./colors.config.json", "utf8"));

const config = {
  settings: {
    hueAdjustment: { maxRotationDeg: 6 },
    contrastTargets: { AAA: 7, AA: 4.5 },
  },
};

let scssOutput = `// Auto-generated OKLCH tokens (Enhanced: adaptive + smooth + harmonic)\n:root {\n`;
let cssOutput = `/* Auto-generated OKLCH tokens (Enhanced: adaptive + smooth + harmonic) */\n:root {\n`;

console.log("\n🎨 Enhanced palette generation with adaptive chroma, smoothness, and hue harmony...\n");

for (const [name, base] of Object.entries(colors)) {
  const result = generatePalette(name, base, config);
  const { palette, baseL, warnings, info, edgeCases } = result;

  scssOutput += `  // ${name.toUpperCase()}\n`;
  cssOutput += `  /* ${name.toUpperCase()} */\n`;

  for (const [variant, data] of Object.entries(palette)) {
    scssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
    cssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
  }

  scssOutput += "\n";
  cssOutput += "\n";

  console.log(`✓ ${name}: base L=${baseL.toFixed(3)}, C=${palette.base.oklch.match(/[\d.]+%/g)[1]}`);
  if (edgeCases.length) edgeCases.forEach((e) => console.log(`  ${e}`));
  info.forEach((i) => console.log(`  ${i}`));
  if (warnings.length) warnings.forEach((w) => console.log(`  ${w}`));
  console.log();
}

scssOutput += "}\n";
cssOutput += "}\n";

fs.writeFileSync("./src/styles/_tokens.generated.scss", scssOutput);
fs.writeFileSync("./src/styles/tokens-generated.css", cssOutput);

console.log("✅ Enhanced color tokens generated successfully!");
