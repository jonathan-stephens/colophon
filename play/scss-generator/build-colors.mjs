// build-colors.mjs (Enhanced: symmetric contrast matrix with precise targets)
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
    currentC *= 0.95;
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

// === Hue harmony adjustments ===
function getHueShift(baseHue, targetL, baseL, dirSign) {
  const h = ((baseHue || 0) % 360 + 360) % 360;

  const isRed = (h >= 0 && h < 30) || h >= 330;
  const isOrange = h >= 30 && h < 60;
  const isYellow = h >= 60 && h < 90;
  const isGreen = h >= 90 && h < 150;
  const isCyan = h >= 150 && h < 210;
  const isBlue = h >= 210 && h < 270;
  const isPurple = h >= 270 && h < 330;

  const dist = Math.abs(targetL - baseL);
  let hueRotation = 0;

  if (dirSign < 0) {
    if (isBlue || isCyan) hueRotation = -8 * Math.pow(dist, 1.1);
    else if (isGreen) hueRotation = 5 * Math.pow(dist, 1.1);
    else if (isYellow || isOrange) hueRotation = -6 * Math.pow(dist, 1.1);
    else if (isRed) hueRotation = 4 * Math.pow(dist, 1.1);
    else if (isPurple) hueRotation = -5 * Math.pow(dist, 1.1);
  } else {
    if (isBlue) hueRotation = 6 * Math.pow(dist, 1.1);
    else if (isGreen) hueRotation = -4 * Math.pow(dist, 1.1);
    else if (isYellow) hueRotation = 3 * Math.pow(dist, 1.1);
    else if (isRed || isOrange) hueRotation = 5 * Math.pow(dist, 1.1);
    else if (isPurple) hueRotation = 8 * Math.pow(dist, 1.1);
  }

  return hueRotation;
}

// === Adaptive chroma ===
function adaptChromaHue(baseColor, targetL, baseL, variant, params) {
  const { c: bC, h: bH } = baseColor.oklch;
  const dirSign = targetL < baseL ? -1 : 1;

  const isAchromatic = bC < 0.005;
  const isNearGray = bC < 0.02 && !isAchromatic;

  if (isAchromatic) return { c: 0.008, h: bH || 0 };

  const targetDistFromMid = Math.abs(targetL - 0.5);

  let chromaFactor;
  if (variant === 'darkest' || variant === 'lightest') {
    chromaFactor = 0.7 + 0.2 * (1 - targetDistFromMid);
  } else if (variant === 'darker' || variant === 'lighter') {
    chromaFactor = Math.max(0.9, 1 - targetDistFromMid * 0.15);
  } else if (variant === 'dark' || variant === 'light' || variant === 'mid') {
    chromaFactor = Math.max(0.95, 1 - targetDistFromMid * 0.1);
  } else {
    chromaFactor = 1 - targetDistFromMid * 0.3;
  }

  if (isNearGray) chromaFactor *= 1.8;
  else if (baseL > 0.85) chromaFactor = Math.max(chromaFactor, 0.85);
  else if (baseL < 0.20) chromaFactor = Math.max(chromaFactor, 0.82);

  let c = bC * chromaFactor;

  let minChroma;
  if (isNearGray) minChroma = 0.018;
  else if (variant === 'darker' || variant === 'lighter') minChroma = 0.03;
  else if (variant === 'mid' || variant === 'dark' || variant === 'light') minChroma = 0.028;
  else minChroma = 0.02;

  c = clamp(c, minChroma, getMaxChromaForHue(bH, targetL));

  const harmonicShift = getHueShift(bH, targetL, baseL, dirSign);
  const h = ((bH + harmonicShift) % 360 + 360) % 360;

  return { c, h };
}

// === NEW: Calculate optimal lightness distribution for symmetric contrast ===
function calculateSymmetricScale(baseL) {
  // Target contrast ratios based on distance:
  // Adjacent (1 step): ~1.5-2.0 (DNE to low AA18)
  // 2 steps away: ~3.0-3.5 (AA18)
  // 3 steps away: ~4.5-7.0 (AA to AAA)

  // We need to find lightness values where:
  // - Each step has contrast ~1.7-2.0 with next step
  // - But cumulative gives us AA at 3 steps, AAA at extremes

  // Start with mid positioned strategically
  let midL = baseL;

  // Adjust mid to ensure good range
  if (baseL < 0.35) {
    midL = Math.max(baseL, 0.40);
  } else if (baseL > 0.75) {
    midL = Math.min(baseL, 0.70);
  } else if (baseL >= 0.35 && baseL < 0.45) {
    midL = Math.max(baseL, 0.45);
  } else if (baseL > 0.65 && baseL <= 0.75) {
    midL = Math.min(baseL, 0.65);
  }

  // Calculate steps to achieve target contrast ratios
  // Working backwards from desired contrast ratios to lightness differences

  // For WCAG contrast, rough formula: higher contrast needs exponential lightness difference
  // Target: 1 step = 1.8x, 2 steps = 3.2x, 3 steps = 5.5x+

  // Define based on mathematical spacing for perceptual uniformity
  const darkestL = Math.max(0.08, midL * 0.16);  // ~6.25x darker
  const darkerL = Math.max(0.12, midL * 0.44);   // ~2.27x darker
  const darkL = Math.max(0.20, midL * 0.72);     // ~1.39x darker

  const lightL = Math.min(0.90, midL * 1.28);    // ~1.28x lighter
  const lighterL = Math.min(0.94, midL * 1.56);  // ~1.56x lighter
  const lightestL = Math.min(0.96, midL * 1.80); // ~1.80x lighter

  return {
    darkest: clamp(darkestL, 0.08, 0.25),
    darker: clamp(darkerL, 0.12, 0.35),
    dark: clamp(darkL, 0.25, 0.45),
    mid: clamp(midL, 0.35, 0.75),
    light: clamp(lightL, 0.60, 0.85),
    lighter: clamp(lighterL, 0.75, 0.94),
    lightest: clamp(lightestL, 0.88, 0.97)
  };
}

// === Enforce symmetric contrast matrix ===
function enforceSymmetricContrast(colorsObj) {
  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const variants = order.filter(v => colorsObj[v]);

  // Target contrast ratios by distance:
  const contrastTargets = {
    1: { min: 1.5, max: 2.2, name: 'Adjacent (DNE)' },      // Next to each other
    2: { min: 2.8, max: 3.8, name: 'AA18' },                // 2 steps away
    3: { min: 4.5, max: 7.5, name: 'AA' },                  // 3 steps away
    4: { min: 7.0, max: 12.0, name: 'AAA' },                // 4+ steps away
    5: { min: 9.0, max: 15.0, name: 'AAA' },
    6: { min: 12.0, max: 21.0, name: 'AAA' }
  };

  let iterations = 0;
  const maxIterations = 50;

  while (iterations < maxIterations) {
    let needsAdjustment = false;

    for (let i = 0; i < variants.length; i++) {
      for (let j = i + 1; j < variants.length; j++) {
        const distance = j - i;
        const v1 = variants[i];
        const v2 = variants[j];
        const target = contrastTargets[distance];

        if (!target) continue;

        const currentContrast = getContrast(colorsObj[v1], colorsObj[v2]);

        // Check if contrast is outside acceptable range
        if (currentContrast < target.min) {
          needsAdjustment = true;

          // Increase contrast by adjusting the extremes
          if (i === 0) {
            // Darken darkest
            const newL = Math.max(0.08, colorsObj[v1].oklch.l - 0.01);
            colorsObj[v1] = createColorOKLCH(newL, colorsObj[v1].oklch.c, colorsObj[v1].oklch.h);
          } else if (j === variants.length - 1) {
            // Lighten lightest
            const newL = Math.min(0.97, colorsObj[v2].oklch.l + 0.01);
            colorsObj[v2] = createColorOKLCH(newL, colorsObj[v2].oklch.c, colorsObj[v2].oklch.h);
          }
        } else if (currentContrast > target.max) {
          needsAdjustment = true;

          // Decrease contrast by adjusting toward mid-range
          if (i === 0) {
            const newL = Math.min(0.20, colorsObj[v1].oklch.l + 0.008);
            colorsObj[v1] = createColorOKLCH(newL, colorsObj[v1].oklch.c, colorsObj[v1].oklch.h);
          } else if (j === variants.length - 1) {
            const newL = Math.max(0.88, colorsObj[v2].oklch.l - 0.008);
            colorsObj[v2] = createColorOKLCH(newL, colorsObj[v2].oklch.c, colorsObj[v2].oklch.h);
          }
        }
      }
    }

    if (!needsAdjustment) break;
    iterations++;
  }

  return colorsObj;
}

// === Perceptual smoothness ===
function ensurePerceptualSmoothness(colorsObj) {
  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const minStep = 0.06;  // Increased for better separation
  const maxStep = 0.22;

  let adjusted = false;

  for (let i = 1; i < order.length; i++) {
    const prev = colorsObj[order[i - 1]];
    const curr = colorsObj[order[i]];
    if (!prev || !curr) continue;

    const step = curr.oklch.l - prev.oklch.l;

    if (step < minStep) {
      const newL = clamp(prev.oklch.l + minStep, 0.08, 0.97);
      colorsObj[order[i]] = createColorOKLCH(newL, curr.oklch.c, curr.oklch.h);
      adjusted = true;
    }

    if (step > maxStep && i > 1) {
      const newL = prev.oklch.l + (step * 0.7);
      colorsObj[order[i]] = createColorOKLCH(newL, curr.oklch.c, curr.oklch.h);
      adjusted = true;
    }
  }

  return { colorsObj, adjusted };
}

// === PALETTE VALIDATION & SCORING ===
function validatePalette(colorsObj, baseColor) {
  const validation = {
    score: 100,
    accessibility: { score: 100, issues: [], passes: [] },
    gamut: { score: 100, issues: [], passes: [] },
    perceptual: { score: 100, issues: [], passes: [] },
    symmetry: { score: 100, issues: [], passes: [] },
    recommendations: []
  };

  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const variants = order.filter(v => colorsObj[v]);

  // Check symmetric contrast matrix requirements
  const matrixChecks = [
    // Distance 1 (adjacent): should be DNE or low
    { pair: ['darkest', 'darker'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },
    { pair: ['darker', 'dark'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },
    { pair: ['dark', 'mid'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },
    { pair: ['mid', 'light'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },
    { pair: ['light', 'lighter'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },
    { pair: ['lighter', 'lightest'], distance: 1, min: 1.5, max: 2.2, level: 'Adjacent' },

    // Distance 2: should be AA18 (3:1)
    { pair: ['darkest', 'dark'], distance: 2, min: 2.8, max: 3.8, level: 'AA18' },
    { pair: ['darker', 'mid'], distance: 2, min: 2.8, max: 3.8, level: 'AA18' },
    { pair: ['dark', 'light'], distance: 2, min: 2.8, max: 3.8, level: 'AA18' },
    { pair: ['mid', 'lighter'], distance: 2, min: 2.8, max: 3.8, level: 'AA18' },
    { pair: ['light', 'lightest'], distance: 2, min: 2.8, max: 3.8, level: 'AA18' },

    // Distance 3: should be AA (4.5:1)
    { pair: ['darkest', 'mid'], distance: 3, min: 4.5, max: 7.5, level: 'AA' },
    { pair: ['darker', 'light'], distance: 3, min: 4.5, max: 7.5, level: 'AA' },
    { pair: ['dark', 'lighter'], distance: 3, min: 4.5, max: 7.5, level: 'AA' },
    { pair: ['mid', 'lightest'], distance: 3, min: 4.5, max: 7.5, level: 'AA' },

    // Distance 4+: should be AAA (7:1+)
    { pair: ['darkest', 'light'], distance: 4, min: 7.0, max: 15.0, level: 'AAA' },
    { pair: ['darker', 'lighter'], distance: 4, min: 7.0, max: 15.0, level: 'AAA' },
    { pair: ['dark', 'lightest'], distance: 4, min: 7.0, max: 15.0, level: 'AAA' },
    { pair: ['darkest', 'lighter'], distance: 5, min: 9.0, max: 18.0, level: 'AAA' },
    { pair: ['darker', 'lightest'], distance: 5, min: 9.0, max: 18.0, level: 'AAA' },
    { pair: ['darkest', 'lightest'], distance: 6, min: 12.0, max: 21.0, level: 'AAA' },
  ];

  matrixChecks.forEach(check => {
    const [c1, c2] = check.pair;
    if (colorsObj[c1] && colorsObj[c2]) {
      const ratio = getContrast(colorsObj[c1], colorsObj[c2]);
      if (ratio >= check.min && ratio <= check.max) {
        validation.symmetry.passes.push(
          `✓ ${c1} ↔ ${c2}: ${ratio.toFixed(2)}:1 (${check.level})`
        );
      } else if (ratio < check.min) {
        validation.symmetry.issues.push(
          `✗ ${c1} ↔ ${c2}: ${ratio.toFixed(2)}:1 (needs ≥${check.min}:1 for ${check.level})`
        );
        validation.symmetry.score -= 5;
      } else {
        validation.symmetry.issues.push(
          `⚠ ${c1} ↔ ${c2}: ${ratio.toFixed(2)}:1 (too high, max ${check.max}:1 for ${check.level})`
        );
        validation.symmetry.score -= 3;
      }
    }
  });

  let gamutIssues = 0;
  Object.entries(colorsObj).forEach(([variant, color]) => {
    if (!isInGamut(color)) {
      validation.gamut.issues.push(
        `⚠ ${variant} out of sRGB gamut (may render inconsistently)`
      );
      gamutIssues++;
    }
  });

  if (gamutIssues > 0) {
    validation.gamut.score = Math.max(0, 100 - (gamutIssues * 10));
  } else {
    validation.gamut.passes.push('✓ All colors in sRGB gamut');
  }

  const lightnessSteps = [];
  for (let i = 1; i < variants.length; i++) {
    const step = colorsObj[variants[i]].oklch.l - colorsObj[variants[i - 1]].oklch.l;
    lightnessSteps.push({ from: variants[i - 1], to: variants[i], step });
  }

  if (lightnessSteps.length > 0) {
    const avgStep = lightnessSteps.reduce((sum, s) => sum + s.step, 0) / lightnessSteps.length;
    let unevenSteps = 0;

    lightnessSteps.forEach(({ from, to, step }) => {
      const deviation = Math.abs(step - avgStep) / avgStep;
      if (deviation > 0.6) {
        validation.perceptual.issues.push(
          `⚠ Uneven step ${from}→${to}: ${(step * 100).toFixed(1)}% (avg: ${(avgStep * 100).toFixed(1)}%)`
        );
        unevenSteps++;
      }
    });

    if (unevenSteps > 0) {
      validation.perceptual.score = Math.max(70, 100 - (unevenSteps * 10));
    } else {
      validation.perceptual.passes.push(
        `✓ Smooth progression (avg step: ${(avgStep * 100).toFixed(1)}%)`
      );
    }
  }

  if (validation.symmetry.score < 100) {
    validation.recommendations.push(
      '💡 Improve symmetry: Adjust lightness distribution for better contrast balance'
    );
  }

  if (validation.gamut.score < 100) {
    validation.recommendations.push(
      '💡 Reduce saturation: Lower chroma to stay in sRGB gamut'
    );
  }

  validation.score = Math.round(
    (validation.symmetry.score * 0.5) +
    (validation.gamut.score * 0.2) +
    (validation.perceptual.score * 0.3)
  );

  return validation;
}

function getScoreGrade(score) {
  if (score >= 95) return '🏆 Excellent';
  if (score >= 85) return '✨ Good';
  if (score >= 70) return '👍 Fair';
  return '⚠️  Needs Work';
}

// === Main palette generation ===
function generatePalette(name, rawEntry, cfg) {
  const entry = typeof rawEntry === "string" ? { base: rawEntry } : { ...rawEntry };
  const baseColor = new Color(entry.base);
  const baseL = baseColor.oklch.l;
  const baseC = baseColor.oklch.c;

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

  if (baseL < 0.35) {
    edgeCases.push("🌑 Dark base - mid adjusted upward for symmetric range");
  } else if (baseL > 0.75) {
    edgeCases.push("☀️  Light base - mid adjusted downward for symmetric range");
  }

  const scale = calculateSymmetricScale(baseL);

  const colorsObj = {};
  const gamutIssues = [];

  for (const [variant, targetL] of Object.entries(scale)) {
    const { c, h } = adaptChromaHue(baseColor, targetL, scale.mid, variant, cfg.settings);

    const result = gamutClipOKLCH(targetL, c, h);
    colorsObj[variant] = result.color;

    if (result.clipped) {
      gamutIssues.push(`${variant}: chroma reduced ${c.toFixed(3)}→${result.finalChroma.toFixed(3)}`);
    }
  }

  // Enforce symmetric contrast matrix
  let constrained = enforceSymmetricContrast(colorsObj);

  const smoothResult = ensurePerceptualSmoothness(constrained);
  constrained = smoothResult.colorsObj;

  if (smoothResult.adjusted) {
    edgeCases.push("🔧 Smoothness adjustments applied");
  }

  const validation = validatePalette(constrained, baseColor);

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

  // Add reference colors for contrast checking
  palette.black = {
    oklch: 'oklch(0% 0 0)',
    hex: '#000000',
    rgb: 'rgb(0, 0, 0)',
  };

  palette.white = {
    oklch: 'oklch(100% 0 0)',
    hex: '#ffffff',
    rgb: 'rgb(255, 255, 255)',
  };

  const { darkest, darker, dark, mid, light, lighter, lightest } = constrained;

  const info = [];

  // Key contrast pairs
  const pair_extremes = getContrast(darkest, lightest).toFixed(2);
  const pair_far = getContrast(darker, lighter).toFixed(2);
  const pair_mid = getContrast(dark, light).toFixed(2);

  info.push(`Key pairs: darkest↔lightest=${pair_extremes}, darker↔lighter=${pair_far}, dark↔light=${pair_mid}`);
  info.push(`Mid contrasts: ↔darkest=${getContrast(mid, darkest).toFixed(2)}, ↔lightest=${getContrast(mid, lightest).toFixed(2)}`);

  if (gamutIssues.length > 0) {
    info.push(`Gamut clipping: ${gamutIssues.join(', ')}`);
  }

  return { name, palette, baseL, warnings, info, edgeCases, validation };
}

// ==========================================================
const colors = JSON.parse(fs.readFileSync("./colors.config.json", "utf8"));

const config = {
  settings: {
    hueAdjustment: { maxRotationDeg: 6 },
  },
};

let scssOutput = `// Auto-generated OKLCH tokens (Symmetric contrast matrix)\n:root {\n`;
let cssOutput = `/* Auto-generated OKLCH tokens (Symmetric contrast matrix) */\n:root {\n`;

console.log("\n🎨 Generating palettes with symmetric contrast matrix...\n");
console.log("=".repeat(80));

const allResults = [];

for (const [name, base] of Object.entries(colors)) {
  const result = generatePalette(name, base, config);
  allResults.push(result);

  const { palette, baseL, warnings, info, edgeCases, validation } = result;

  scssOutput += `  // ${name.toUpperCase()}\n`;
  cssOutput += `  /* ${name.toUpperCase()} */\n`;

  for (const [variant, data] of Object.entries(palette)) {
    scssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
    cssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
  }

  scssOutput += "\n";
  cssOutput += "\n";

  console.log(`\n${name.toUpperCase()}`);
  console.log("-".repeat(80));
  console.log(`Base: L=${baseL.toFixed(3)}, C=${palette.base.oklch.match(/[\d.]+%/g)?.[1] || 'N/A'}`);

  console.log(`\n📊 QUALITY SCORE: ${validation.score}/100 ${getScoreGrade(validation.score)}`);
  console.log(`   • Symmetric Matrix: ${validation.symmetry.score}/100`);
  console.log(`   • Gamut Coverage: ${validation.gamut.score}/100`);
  console.log(`   • Perceptual: ${validation.perceptual.score}/100`);

  if (edgeCases.length) {
    console.log(`\n${edgeCases.join('\n')}`);
  }

  const allPasses = [...validation.symmetry.passes, ...validation.gamut.passes, ...validation.perceptual.passes];
  if (allPasses.length > 0 && allPasses.length <= 10) {
    console.log(`\n✅ Sample Passes:`);
    allPasses.slice(0, 5).forEach(pass => console.log(`   ${pass}`));
    if (allPasses.length > 5) console.log(`   ... and ${allPasses.length - 5} more`);
  }

  const allIssues = [...validation.symmetry.issues, ...validation.gamut.issues, ...validation.perceptual.issues];
  if (allIssues.length > 0) {
    console.log(`\n⚠️  Issues Found (${allIssues.length}):`);
    allIssues.slice(0, 8).forEach(issue => console.log(`   ${issue}`));
    if (allIssues.length > 8) console.log(`   ... and ${allIssues.length - 8} more`);
  }

  if (validation.recommendations.length > 0) {
    console.log(`\n💡 Recommendations:`);
    validation.recommendations.forEach(rec => console.log(`   ${rec}`));
  }

  if (info.length) {
    console.log(`\nℹ️  Details:`);
    info.forEach((i) => console.log(`   ${i}`));
  }

  console.log();
}

scssOutput += "}\n";
cssOutput += "}\n";

fs.writeFileSync("./src/styles/_tokens.generated.scss", scssOutput);
fs.writeFileSync("./src/styles/tokens-generated.css", cssOutput);

// === Generate HTML Report ===
const totalPalettes = allResults.length;
const avgScore = Math.round(allResults.reduce((sum, r) => sum + r.validation.score, 0) / totalPalettes);
const excellentCount = allResults.filter(r => r.validation.score >= 95).length;
const issuesCount = allResults.reduce((sum, r) => {
  return sum + r.validation.symmetry.issues.length +
         r.validation.gamut.issues.length +
         r.validation.perceptual.issues.length;
}, 0);

let htmlReport = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OKLCH Palette Report - Symmetric Contrast Matrix</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; color: #1a1a1a; background: #fafafa; padding: 2rem; }
    .container { max-width: 1400px; margin: 0 auto; }
    header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 1rem; margin-bottom: 2rem; position: relative; overflow: hidden; }
    header::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; }
    .header-content { position: relative; z-index: 1; }
    h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
    .subtitle { opacity: 0.9; font-size: 1.125rem; }
    .summary-dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .summary-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #3b82f6; }
    .summary-card.excellent { border-left-color: #10b981; }
    .summary-card.average { border-left-color: #8b5cf6; }
    .summary-card.issues { border-left-color: #ef4444; }
    .summary-label { font-size: 0.875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .summary-value { font-size: 2.5rem; font-weight: 800; color: #1a1a1a; }
    .summary-detail { font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem; }
    .toc { background: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .toc-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
    .toc-list { list-style: none; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem; }
    .toc-item a { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: #f9fafb; border-radius: 0.5rem; text-decoration: none; color: #1a1a1a; font-weight: 500; transition: all 0.2s; }
    .toc-item a:hover { background: #f3f4f6; transform: translateX(4px); }
    .toc-score { font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; }
    .toc-score.excellent { background: #d1fae5; color: #065f46; }
    .toc-score.good { background: #dbeafe; color: #1e40af; }
    .toc-score.fair { background: #fef3c7; color: #92400e; }
    .toc-score.poor { background: #fee2e2; color: #991b1b; }
    .palette-section { background: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); scroll-margin-top: 2rem; }
    .palette-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0; }
    .palette-name { font-size: 1.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .score-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 2rem; font-weight: 700; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
    .score-breakdown { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .score-item { background: #f9fafb; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #3b82f6; }
    .score-item-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.25rem; }
    .score-item-value { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; }
    .color-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .color-swatch { border-radius: 0.5rem; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
    .color-swatch:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .swatch-color { height: 100px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; position: relative; }
    .copy-indicator { position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.6); color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; opacity: 0; transition: opacity 0.2s; }
    .color-swatch:hover .copy-indicator { opacity: 1; }
    .swatch-info { background: #f9fafb; padding: 0.75rem; }
    .swatch-name { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.5rem; color: #374151; }
    .swatch-value { font-family: 'SF Mono', Monaco, monospace; font-size: 0.7rem; color: #6b7280; margin-bottom: 0.25rem; }
    .contrast-matrix { background: #f9fafb; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; }
    .contrast-matrix-title { font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem; }
    .matrix-description { font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; }
    .contrast-table-wrapper { overflow-x: auto; margin-bottom: 1rem; }
    .contrast-table { width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden; }
    .contrast-table th, .contrast-table td { padding: 0.75rem; text-align: center; border: 1px solid #e5e7eb; }
    .contrast-table thead th { background: #f9fafb; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151; }
    .contrast-table tbody th { background: #f9fafb; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #374151; text-align: left; }
    .corner-cell { background: #f3f4f6 !important; }
    .variant-header { white-space: nowrap; }
    .same-color { background: #f9fafb; color: #9ca3af; font-weight: 700; }
    .contrast-cell { padding: 0.5rem !important; }
    .contrast-value { font-size: 0.875rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
    .contrast-badge { display: inline-block; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .contrast-badge.aaa { background: #d1fae5; color: #065f46; }
    .contrast-badge.aa { background: #dbeafe; color: #1e40af; }
    .contrast-badge.aa18 { background: #fef3c7; color: #92400e; }
    .contrast-badge.dne { background: #fee2e2; color: #991b1b; }
    .contrast-badge.adj { background: #f3f4f6; color: #6b7280; }
    .matrix-legend { background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; }
    .legend-title { font-weight: 700; font-size: 0.875rem; margin-bottom: 0.5rem; color: #374151; }
    .legend-items { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; }
    .legend-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #6b7280; }
    .legend-badge { display: inline-block; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .validation-section { margin-top: 1.5rem; }
    .validation-title { font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .validation-list { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
    .validation-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; }
    .validation-item.pass { background: #f0fdf4; border-left: 4px solid #10b981; }
    .validation-item.issue { background: #fef2f2; border-left: 4px solid #ef4444; }
    .validation-item.warning { background: #fffbeb; border-left: 4px solid #f59e0b; }
    .validation-item.info { background: #eff6ff; border-left: 4px solid #3b82f6; }
    .validation-item.recommendation { background: #f5f3ff; border-left: 4px solid #8b5cf6; }
    .edge-cases { background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem; }
    .edge-cases-title { font-weight: 700; margin-bottom: 0.5rem; color: #1e40af; }
    .back-to-top { position: fixed; bottom: 2rem; right: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s, transform 0.3s; z-index: 1000; }
    .back-to-top.visible { opacity: 1; }
    .back-to-top:hover { transform: translateY(-4px); }
    footer { text-align: center; padding: 2rem; color: #6b7280; font-size: 0.875rem; }

    /* Filter Controls */
    .filter-controls { background: white; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e5e7eb; }
    .filter-controls-title { font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem; color: #374151; }
    .filter-buttons { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .filter-btn { padding: 0.5rem 1rem; border-radius: 0.375rem; border: 2px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; }
    .filter-btn:hover { border-color: #667eea; }
    .filter-btn.active { background: #667eea; color: white; border-color: #667eea; }
    .filter-btn.reset { border-color: #ef4444; color: #ef4444; }
    .filter-btn.reset:hover { background: #ef4444; color: white; }

    /* Comparison Mode */
    .comparison-toggle { background: white; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
    .comparison-label { font-weight: 600; font-size: 0.875rem; color: #374151; }
    .toggle-switch { position: relative; display: inline-block; width: 48px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: .3s; border-radius: 24px; }
    .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
    input:checked + .toggle-slider { background-color: #667eea; }
    input:checked + .toggle-slider:before { transform: translateX(24px); }
    .comparison-mode .palette-section { display: inline-block; width: calc(50% - 0.5rem); vertical-align: top; margin-right: 1rem; }
    .comparison-mode .palette-section:nth-child(even) { margin-right: 0; }
    .comparison-info { font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem; }

    @media print { .back-to-top { display: none; } .toc { page-break-after: always; } .palette-section { page-break-inside: avoid; } }
    @media (max-width: 768px) {
      .comparison-mode .palette-section { width: 100%; margin-right: 0; display: block; }
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="header-content">
        <h1>🎨 OKLCH Palette Report</h1>
        <p class="subtitle">Symmetric Contrast Matrix • Generated: ${new Date().toLocaleString()} • ${totalPalettes} Palette${totalPalettes > 1 ? 's' : ''}</p>
      </div>
    </header>
    <div class="summary-dashboard">
      <div class="summary-card">
        <div class="summary-label">Total Palettes</div>
        <div class="summary-value">${totalPalettes}</div>
        <div class="summary-detail">Analyzed and validated</div>
      </div>
      <div class="summary-card average">
        <div class="summary-label">Average Score</div>
        <div class="summary-value">${avgScore}</div>
        <div class="summary-detail">Out of 100 points</div>
      </div>
      <div class="summary-card excellent">
        <div class="summary-label">Excellent Palettes</div>
        <div class="summary-value">${excellentCount}</div>
        <div class="summary-detail">Score ≥ 95</div>
      </div>
      <div class="summary-card issues">
        <div class="summary-label">Total Issues</div>
        <div class="summary-value">${issuesCount}</div>
        <div class="summary-detail">Across all palettes</div>
      </div>
    </div>
    <div class="toc">
      <div class="toc-title">📑 Table of Contents</div>
      <ul class="toc-list">`;

allResults.forEach(({ name, validation }) => {
  const scoreClass = validation.score >= 95 ? 'excellent' : validation.score >= 85 ? 'good' : validation.score >= 70 ? 'fair' : 'poor';
  htmlReport += `<li class="toc-item"><a href="#palette-${name}"><span>${name}</span><span class="toc-score ${scoreClass}">${validation.score}</span></a></li>`;
});

htmlReport += `</ul></div>`;

// Add comparison mode toggle
htmlReport += `
  <div class="comparison-toggle">
    <div>
      <div class="comparison-label">🔄 Comparison Mode</div>
      <div class="comparison-info">View palettes side-by-side for easy comparison</div>
    </div>
    <label class="toggle-switch">
      <input type="checkbox" id="comparisonToggle" onchange="toggleComparisonMode()">
      <span class="toggle-slider"></span>
    </label>
  </div>
`;

htmlReport += `<div id="palettesContainer">`;

allResults.forEach(({ name, palette, validation, edgeCases, info }) => {
  const getScoreColor = (score) => score >= 95 ? '#10b981' : score >= 85 ? '#3b82f6' : score >= 70 ? '#f59e0b' : '#ef4444';
  const getScoreEmoji = (score) => score >= 95 ? '🏆' : score >= 85 ? '✨' : score >= 70 ? '👍' : '⚠️';

  htmlReport += `<div class="palette-section" id="palette-${name}">
    <div class="palette-header">
      <div class="palette-name">${name}</div>
      <div class="score-badge" style="background: ${getScoreColor(validation.score)}">
        <span>${getScoreEmoji(validation.score)}</span><span>${validation.score}/100</span>
      </div>
    </div>
    <div class="score-breakdown">
      <div class="score-item">
        <div class="score-item-label">Symmetric Matrix</div>
        <div class="score-item-value">${validation.symmetry.score}/100</div>
      </div>
      <div class="score-item" style="border-left-color: #10b981">
        <div class="score-item-label">Gamut Coverage</div>
        <div class="score-item-value">${validation.gamut.score}/100</div>
      </div>
      <div class="score-item" style="border-left-color: #8b5cf6">
        <div class="score-item-label">Perceptual</div>
        <div class="score-item-value">${validation.perceptual.score}/100</div>
      </div>
    </div>`;

  if (edgeCases.length > 0) {
    htmlReport += `<div class="edge-cases"><div class="edge-cases-title">Edge Cases Detected</div>${edgeCases.map(ec => `<div>${ec}</div>`).join('')}</div>`;
  }

  // Add base color swatch (full width)
  htmlReport += `<div style="margin-bottom: 1.5rem;">
    <div class="color-swatch" style="grid-column: 1 / -1;" onclick="copyColor('${palette.base.hex}', this)">
      <div class="swatch-color" style="background: ${palette.base.hex}; color: ${new Color(palette.base.hex).oklch.l > 0.5 ? '#000000' : '#ffffff'}">base (original)<span class="copy-indicator">Click to copy</span></div>
      <div class="swatch-info">
        <div class="swatch-name">base</div>
        <div class="swatch-value">${palette.base.hex}</div>
        <div class="swatch-value">${palette.base.oklch}</div>
      </div>
    </div>
  </div>`;

  htmlReport += `<div class="color-grid">`;

  // Add black swatch first
  htmlReport += `<div class="color-swatch" onclick="copyColor('#000000', this)">
    <div class="swatch-color" style="background: #000000; color: #ffffff">black<span class="copy-indicator">Click to copy</span></div>
    <div class="swatch-info">
      <div class="swatch-name">black</div>
      <div class="swatch-value">#000000</div>
      <div class="swatch-value">oklch(0% 0 0)</div>
    </div>
  </div>`;

  // Add all palette colors in order
  const colorOrder = ["darkest", "darker", "dark", "mid", "light", "lighter", "lightest"];
  colorOrder.forEach(variant => {
    if (palette[variant]) {
      const data = palette[variant];
      const bgColor = data.hex;
      const textColor = new Color(bgColor).oklch.l > 0.5 ? '#000000' : '#ffffff';
      htmlReport += `<div class="color-swatch" onclick="copyColor('${data.hex}', this)">
        <div class="swatch-color" style="background: ${bgColor}; color: ${textColor}">${variant}<span class="copy-indicator">Click to copy</span></div>
        <div class="swatch-info">
          <div class="swatch-name">${variant}</div>
          <div class="swatch-value">${data.hex}</div>
          <div class="swatch-value">${data.oklch}</div>
        </div>
      </div>`;
    }
  });

  // Add white swatch last
  htmlReport += `<div class="color-swatch" onclick="copyColor('#ffffff', this)">
    <div class="swatch-color" style="background: #ffffff; color: #000000">white<span class="copy-indicator">Click to copy</span></div>
    <div class="swatch-info">
      <div class="swatch-name">white</div>
      <div class="swatch-value">#ffffff</div>
      <div class="swatch-value">oklch(100% 0 0)</div>
    </div>
  </div>`;

  htmlReport += `</div>`;

  // Add comprehensive contrast grid with base, black, and white
  const variantOrder = ["black", "darkest", "darker", "dark", "mid", "light", "lighter", "lightest", "white", "base"];
  const availableVariants = variantOrder.filter(v => palette[v]);

  htmlReport += `
    <div class="contrast-matrix">
      <div class="contrast-matrix-title">📊 Full Contrast Matrix (with reference colors)</div>
      <div class="matrix-description">Includes base color, pure black (#000), and pure white (#fff) for reference. Generated palette variants target symmetric contrast patterns.</div>

      <!-- Filter Controls -->
      <div class="filter-controls">
        <div class="filter-controls-title">Filter by Accessibility Level:</div>
        <div class="filter-buttons">
          <button class="filter-btn active" onclick="filterMatrix('${name}', 'all')">All</button>
          <button class="filter-btn" onclick="filterMatrix('${name}', 'aaa')">AAA (7:1+)</button>
          <button class="filter-btn" onclick="filterMatrix('${name}', 'aa')">AA (4.5:1+)</button>
          <button class="filter-btn" onclick="filterMatrix('${name}', 'aa18')">AA18 (3:1+)</button>
          <button class="filter-btn" onclick="filterMatrix('${name}', 'adj')">Adjacent</button>
          <button class="filter-btn reset" onclick="filterMatrix('${name}', 'all')">Reset</button>
        </div>
      </div>

      <div class="contrast-table-wrapper">
        <table class="contrast-table" id="contrast-matrix-${name}">
          <thead>
            <tr>
              <th class="corner-cell">BG → FG ↓</th>`;

  availableVariants.forEach(variant => {
    htmlReport += `<th class="variant-header">${variant}</th>`;
  });

  htmlReport += `</tr></thead><tbody>`;

  availableVariants.forEach((bgVariant, bgIdx) => {
    htmlReport += `<tr data-bg="${bgVariant}">`;
    htmlReport += `<th class="variant-header">${bgVariant}</th>`;

    availableVariants.forEach((fgVariant, fgIdx) => {
      if (bgVariant === fgVariant) {
        htmlReport += `<td class="same-color" data-level="same">—</td>`;
      } else {
        const bgColor = new Color(palette[bgVariant].hex);
        const fgColor = new Color(palette[fgVariant].hex);
        const ratio = getContrast(bgColor, fgColor);

        // Determine badge based on variant type and distance
        const isReference = ['black', 'white', 'base'].includes(bgVariant) || ['black', 'white', 'base'].includes(fgVariant);

        let badge = 'DNE';
        let badgeClass = 'dne';

        if (isReference) {
          // For reference colors, just show the level achieved
          if (ratio >= 7) {
            badge = 'AAA';
            badgeClass = 'aaa';
          } else if (ratio >= 4.5) {
            badge = 'AA';
            badgeClass = 'aa';
          } else if (ratio >= 3) {
            badge = 'AA18';
            badgeClass = 'aa18';
          } else {
            badge = 'DNE';
            badgeClass = 'dne';
          }
        } else {
          // For generated palette colors, use distance-based logic
          const generatedOrder = ["darkest", "darker", "dark", "mid", "light", "lighter", "lightest"];
          const bg = generatedOrder.indexOf(bgVariant);
          const fg = generatedOrder.indexOf(fgVariant);

          if (bg !== -1 && fg !== -1) {
            const distance = Math.abs(fg - bg);

            if (distance === 1) {
              badge = ratio < 2.2 ? 'ADJ' : 'AA18';
              badgeClass = ratio < 2.2 ? 'adj' : 'aa18';
            } else if (distance === 2) {
              if (ratio >= 3) {
                badge = 'AA18';
                badgeClass = 'aa18';
              } else {
                badge = 'DNE';
                badgeClass = 'dne';
              }
            } else if (distance === 3) {
              if (ratio >= 7) {
                badge = 'AAA';
                badgeClass = 'aaa';
              } else if (ratio >= 4.5) {
                badge = 'AA';
                badgeClass = 'aa';
              } else if (ratio >= 3) {
                badge = 'AA18';
                badgeClass = 'aa18';
              }
            } else {
              if (ratio >= 7) {
                badge = 'AAA';
                badgeClass = 'aaa';
              } else if (ratio >= 4.5) {
                badge = 'AA';
                badgeClass = 'aa';
              } else if (ratio >= 3) {
                badge = 'AA18';
                badgeClass = 'aa18';
              }
            }
          }
        }

        htmlReport += `
          <td class="contrast-cell" data-level="${badgeClass}">
            <div class="contrast-value">${ratio.toFixed(2)}</div>
            <div class="contrast-badge ${badgeClass}">${badge}</div>
          </td>`;
      }
    });

    htmlReport += `</tr>`;
  });

  htmlReport += `</tbody></table></div>
    <div class="matrix-legend">
      <div class="legend-title">Legend:</div>
      <div class="legend-items">
        <div class="legend-item"><span class="legend-badge adj">ADJ</span> Adjacent (low contrast)</div>
        <div class="legend-item"><span class="legend-badge aa18">AA18</span> ≥3:1 (Large text 18pt+)</div>
        <div class="legend-item"><span class="legend-badge aa">AA</span> ≥4.5:1 (Normal text)</div>
        <div class="legend-item"><span class="legend-badge aaa">AAA</span> ≥7:1 (Best)</div>
        <div class="legend-item"><span class="legend-badge dne">DNE</span> Below target</div>
      </div>
      <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #6b7280;">
        <strong>Note:</strong> Black, white, and base colors are shown for reference only and don't follow symmetric matrix rules.
      </div>
    </div>
  </div>`;

  const allPasses = [...validation.symmetry.passes, ...validation.gamut.passes, ...validation.perceptual.passes];
  if (allPasses.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">✅ Passed Checks (${allPasses.length})</div><ul class="validation-list">`;
    allPasses.slice(0, 10).forEach(pass => htmlReport += `<li class="validation-item pass">${pass}</li>`);
    if (allPasses.length > 10) htmlReport += `<li class="validation-item info">... and ${allPasses.length - 10} more passing checks</li>`;
    htmlReport += `</ul></div>`;
  }

  const allIssues = [...validation.symmetry.issues, ...validation.gamut.issues, ...validation.perceptual.issues];
  if (allIssues.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">⚠️ Issues Found (${allIssues.length})</div><ul class="validation-list">`;
    allIssues.slice(0, 10).forEach(issue => {
      const issueClass = issue.includes('✗') ? 'issue' : issue.includes('⚠') ? 'warning' : 'info';
      htmlReport += `<li class="validation-item ${issueClass}">${issue}</li>`;
    });
    if (allIssues.length > 10) htmlReport += `<li class="validation-item warning">... and ${allIssues.length - 10} more issues</li>`;
    htmlReport += `</ul></div>`;
  }

  if (validation.recommendations.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">💡 Recommendations</div><ul class="validation-list">`;
    validation.recommendations.forEach(rec => htmlReport += `<li class="validation-item recommendation">${rec}</li>`);
    htmlReport += `</ul></div>`;
  }

  htmlReport += `</div>`;
});

htmlReport += `</div>`; // Close palettesContainer

htmlReport += `<div class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">↑</div>
    <footer>Generated by OKLCH Palette Generator with Symmetric Contrast Matrix<br><strong>${totalPalettes}</strong> palettes analyzed • Average quality score: <strong>${avgScore}/100</strong></footer>
  </div>
  <script>
    function copyColor(color, element) {
      navigator.clipboard.writeText(color).then(() => {
        const indicator = element.querySelector('.copy-indicator');
        const originalText = indicator.textContent;
        indicator.textContent = 'Copied!';
        indicator.style.opacity = '1';
        setTimeout(() => { indicator.textContent = originalText; indicator.style.opacity = ''; }, 1500);
      });
    }

    window.addEventListener('scroll', () => {
      const btn = document.querySelector('.back-to-top');
      if (window.scrollY > 300) { btn.classList.add('visible'); } else { btn.classList.remove('visible'); }
    });

    // Filter matrix functionality
    function filterMatrix(paletteName, level) {
      const table = document.getElementById('contrast-matrix-' + paletteName);
      const rows = table.querySelectorAll('tbody tr');
      const filterButtons = table.closest('.contrast-matrix').querySelectorAll('.filter-btn:not(.reset)');

      // Update button states
      filterButtons.forEach(btn => {
        if (btn.textContent.toLowerCase().includes(level) || (level === 'all' && btn.textContent === 'All')) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });

      if (level === 'all') {
        // Show all cells
        rows.forEach(row => {
          row.style.display = '';
          const cells = row.querySelectorAll('td');
          cells.forEach(cell => {
            cell.style.display = '';
          });
        });
        return;
      }

      // Filter cells based on level
      rows.forEach(row => {
        const cells = row.querySelectorAll('td[data-level]');
        let hasVisibleCell = false;

        cells.forEach(cell => {
          const cellLevel = cell.getAttribute('data-level');

          if (level === 'adj' && cellLevel === 'adj') {
            cell.style.display = '';
            hasVisibleCell = true;
          } else if (level === 'aa18' && (cellLevel === 'aa18' || cellLevel === 'aa' || cellLevel === 'aaa')) {
            cell.style.display = '';
            hasVisibleCell = true;
          } else if (level === 'aa' && (cellLevel === 'aa' || cellLevel === 'aaa')) {
            cell.style.display = '';
            hasVisibleCell = true;
          } else if (level === 'aaa' && cellLevel === 'aaa') {
            cell.style.display = '';
            hasVisibleCell = true;
          } else if (cellLevel === 'same') {
            cell.style.display = '';
          } else {
            cell.style.display = 'none';
          }
        });

        // Hide row if no cells are visible
        row.style.display = hasVisibleCell ? '' : 'none';
      });
    }

    // Comparison mode functionality
    function toggleComparisonMode() {
      const container = document.getElementById('palettesContainer');
      const isChecked = document.getElementById('comparisonToggle').checked;

      if (isChecked) {
        container.classList.add('comparison-mode');
      } else {
        container.classList.remove('comparison-mode');
      }
    }
  </script>
</body>
</html>`;

fs.writeFileSync("./palette-report.html", htmlReport);

console.log("=".repeat(80));
console.log("✅ Color tokens with symmetric contrast matrix generated!");
console.log("\n📁 Output files:");
console.log("   • ./src/styles/_tokens.generated.scss");
console.log("   • ./src/styles/tokens-generated.css");
console.log("   • ./palette-report.html (📊 View symmetric matrix results)");
console.log("\n📊 Report Summary:");
console.log(`   • Total Palettes: ${totalPalettes}`);
console.log(`   • Average Score: ${avgScore}/100`);
console.log(`   • Excellent (≥95): ${excellentCount}`);
console.log(`   • Total Issues: ${issuesCount}`);
console.log("\n🎯 Symmetric Contrast Matrix:");
console.log("   • Adjacent colors (1 step): Low contrast (DNE/ADJ)");
console.log("   • 2-step spacing: AA18 (3:1)");
console.log("   • 3-step spacing: AA (4.5:1)");
console.log("   • 4+ step spacing: AAA (7:1+)");
console.log();
