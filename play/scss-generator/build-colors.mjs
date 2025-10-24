// build-colors.mjs (Enhanced: adaptive chroma, smooth steps, hue harmony, validation & scoring)
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

// === Contrast pair generation ===
function generateContrastPairs(baseL) {
  let midL = baseL;

  if (baseL < 0.30) midL = Math.max(baseL, 0.32);
  else if (baseL > 0.80) midL = Math.min(baseL, 0.78);

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

// === Perceptual smoothness ===
function ensurePerceptualSmoothness(colorsObj) {
  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const minStep = 0.04;
  const maxStep = 0.20;

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
      const newL = prev.oklch.l + (step * 0.6);
      colorsObj[order[i]] = createColorOKLCH(newL, curr.oklch.c, curr.oklch.h);
      adjusted = true;
    }
  }

  return { colorsObj, adjusted };
}

// === Contrast enforcement ===
function enforceContrastPairs(colorsObj, contrastTargets) {
  let attempts = 0;
  while (colorsObj.darkest && colorsObj.lightest &&
         getContrast(colorsObj.darkest, colorsObj.lightest) < contrastTargets.AAA &&
         attempts < 25) {
    const darkL = Math.max(0.08, colorsObj.darkest.oklch.l - 0.02);
    const lightL = Math.min(0.97, colorsObj.lightest.oklch.l + 0.02);

    colorsObj.darkest = createColorOKLCH(darkL, colorsObj.darkest.oklch.c, colorsObj.darkest.oklch.h);
    colorsObj.lightest = createColorOKLCH(lightL, colorsObj.lightest.oklch.c, colorsObj.lightest.oklch.h);
    attempts++;
  }

  attempts = 0;
  while (colorsObj.darker && colorsObj.lighter &&
         getContrast(colorsObj.darker, colorsObj.lighter) < contrastTargets.AA &&
         attempts < 20) {
    const darkL = Math.max(0.10, colorsObj.darker.oklch.l - 0.015);
    const lightL = Math.min(0.95, colorsObj.lighter.oklch.l + 0.015);

    colorsObj.darker = createColorOKLCH(darkL, colorsObj.darker.oklch.c, colorsObj.darker.oklch.h);
    colorsObj.lighter = createColorOKLCH(lightL, colorsObj.lighter.oklch.c, colorsObj.lighter.oklch.h);
    attempts++;
  }

  attempts = 0;
  while (colorsObj.dark && colorsObj.light &&
         getContrast(colorsObj.dark, colorsObj.light) < 3 &&
         attempts < 15) {
    const darkL = Math.max(0.15, colorsObj.dark.oklch.l - 0.01);
    const lightL = Math.min(0.90, colorsObj.light.oklch.l + 0.01);

    colorsObj.dark = createColorOKLCH(darkL, colorsObj.dark.oklch.c, colorsObj.dark.oklch.h);
    colorsObj.light = createColorOKLCH(lightL, colorsObj.light.oklch.c, colorsObj.light.oklch.h);
    attempts++;
  }

  return colorsObj;
}

// === PALETTE VALIDATION & SCORING ===
function validatePalette(colorsObj, baseColor) {
  const validation = {
    score: 100,
    accessibility: { score: 100, issues: [], passes: [] },
    gamut: { score: 100, issues: [], passes: [] },
    perceptual: { score: 100, issues: [], passes: [] },
    recommendations: []
  };

  const contrastChecks = [
    { pair: ['darkest', 'lightest'], target: 7, level: 'AAA' },
    { pair: ['darker', 'lighter'], target: 4.5, level: 'AA' },
    { pair: ['dark', 'light'], target: 3, level: 'AA Large' }
  ];

  contrastChecks.forEach(check => {
    const [c1, c2] = check.pair;
    if (colorsObj[c1] && colorsObj[c2]) {
      const ratio = getContrast(colorsObj[c1], colorsObj[c2]);
      if (ratio >= check.target) {
        validation.accessibility.passes.push(
          `✓ ${c1} ↔ ${c2}: ${ratio.toFixed(2)}:1 (${check.level})`
        );
      } else {
        validation.accessibility.issues.push(
          `✗ ${c1} ↔ ${c2}: ${ratio.toFixed(2)}:1 (needs ${check.level}: ${check.target}:1)`
        );
        validation.accessibility.score -= 15;
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

  const order = ['darkest', 'darker', 'dark', 'mid', 'light', 'lighter', 'lightest'];
  const variants = order.filter(v => colorsObj[v]);

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
      if (deviation > 0.5) {
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

  const chromas = variants.map(v => colorsObj[v].oklch.c);
  const maxChroma = Math.max(...chromas);
  const minChroma = Math.min(...chromas);
  const chromaRange = maxChroma - minChroma;

  if (chromaRange > 0.15) {
    validation.perceptual.issues.push(
      `ℹ Wide chroma variation: ${minChroma.toFixed(3)} to ${maxChroma.toFixed(3)}`
    );
  }

  if (validation.accessibility.score < 100) {
    validation.recommendations.push(
      '💡 Improve contrast: Use darker/lighter variants for better accessibility'
    );
  }

  if (validation.gamut.score < 100) {
    validation.recommendations.push(
      '💡 Reduce saturation: Lower chroma to stay in sRGB gamut'
    );
  }

  if (validation.perceptual.score < 90) {
    validation.recommendations.push(
      '💡 Adjust spacing: Consider more even lightness distribution'
    );
  }

  const baseL = baseColor.oklch.l;
  const baseC = baseColor.oklch.c;

  if (baseC < 0.02) {
    validation.recommendations.push(
      'ℹ Low saturation base: Consider a more vibrant starting color'
    );
  }

  if (baseL < 0.20 || baseL > 0.85) {
    validation.recommendations.push(
      'ℹ Extreme lightness base: May limit palette range'
    );
  }

  validation.score = Math.round(
    (validation.accessibility.score * 0.5) +
    (validation.gamut.score * 0.3) +
    (validation.perceptual.score * 0.2)
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
  const { contrastTargets } = cfg.settings;

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

  const scale = generateContrastPairs(baseL);

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

  let constrained = enforceContrastPairs(colorsObj, contrastTargets);

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

  return { name, palette, baseL, warnings, info, edgeCases, validation };
}

// ==========================================================
const colors = JSON.parse(fs.readFileSync("./colors.config.json", "utf8"));

const config = {
  settings: {
    hueAdjustment: { maxRotationDeg: 6 },
    contrastTargets: { AAA: 7, AA: 4.5 },
  },
};

let scssOutput = `// Auto-generated OKLCH tokens (Enhanced: adaptive + smooth + harmonic + validated)\n:root {\n`;
let cssOutput = `/* Auto-generated OKLCH tokens (Enhanced: adaptive + smooth + harmonic + validated) */\n:root {\n`;

console.log("\n🎨 Enhanced palette generation with validation & scoring...\n");
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
  console.log(`Base: L=${baseL.toFixed(3)}, C=${palette.base.oklch.match(/[\d.]+%/g)[1]}`);

  console.log(`\n📊 QUALITY SCORE: ${validation.score}/100 ${getScoreGrade(validation.score)}`);
  console.log(`   • Accessibility: ${validation.accessibility.score}/100`);
  console.log(`   • Gamut Coverage: ${validation.gamut.score}/100`);
  console.log(`   • Perceptual: ${validation.perceptual.score}/100`);

  if (edgeCases.length) {
    console.log(`\n${edgeCases.join('\n')}`);
  }

  if (validation.accessibility.passes.length > 0 ||
      validation.gamut.passes.length > 0 ||
      validation.perceptual.passes.length > 0) {
    console.log(`\n✅ Passed Checks:`);
    [...validation.accessibility.passes, ...validation.gamut.passes, ...validation.perceptual.passes]
      .forEach(pass => console.log(`   ${pass}`));
  }

  const allIssues = [
    ...validation.accessibility.issues,
    ...validation.gamut.issues,
    ...validation.perceptual.issues
  ];

  if (allIssues.length > 0) {
    console.log(`\n⚠️  Issues Found:`);
    allIssues.forEach(issue => console.log(`   ${issue}`));
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
  return sum + r.validation.accessibility.issues.length +
         r.validation.gamut.issues.length +
         r.validation.perceptual.issues.length;
}, 0);

let htmlReport = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OKLCH Palette Generation Report</title>
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
    .contrast-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; }
    .contrast-pair { background: white; padding: 0.75rem; border-radius: 0.5rem; border-left: 4px solid #e5e7eb; }
    .contrast-pair.pass { border-left-color: #10b981; }
    .contrast-pair.fail { border-left-color: #ef4444; }
    .contrast-pair-label { font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem; }
    .contrast-pair-value { font-size: 1.25rem; font-weight: 700; color: #1a1a1a; }
    .contrast-pair-status { font-size: 0.7rem; margin-top: 0.25rem; font-weight: 600; }
    .contrast-pair.pass .contrast-pair-status { color: #10b981; }
    .contrast-pair.fail .contrast-pair-status { color: #ef4444; }
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
    @media print { .back-to-top { display: none; } .toc { page-break-after: always; } .palette-section { page-break-inside: avoid; } }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="header-content">
        <h1>🎨 OKLCH Palette Generation Report</h1>
        <p class="subtitle">Generated: ${new Date().toLocaleString()} • ${totalPalettes} Palette${totalPalettes > 1 ? 's' : ''}</p>
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
        <div class="score-item-label">Accessibility</div>
        <div class="score-item-value">${validation.accessibility.score}/100</div>
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

  htmlReport += `<div class="color-grid">`;

  Object.entries(palette).forEach(([variant, data]) => {
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
  });

  htmlReport += `</div><div class="contrast-matrix"><div class="contrast-matrix-title">🔍 Contrast Ratios</div><div class="contrast-grid">`;

  if (palette.darkest && palette.lightest) {
    const ratio = info[0].match(/darkest↔lightest=([\d.]+)/)?.[1] || 'N/A';
    const pass = parseFloat(ratio) >= 7;
    htmlReport += `<div class="contrast-pair ${pass ? 'pass' : 'fail'}">
      <div class="contrast-pair-label">darkest ↔ lightest</div>
      <div class="contrast-pair-value">${ratio}:1</div>
      <div class="contrast-pair-status">${pass ? '✓ AAA (7:1)' : '✗ Needs AAA'}</div>
    </div>`;
  }

  if (palette.darker && palette.lighter) {
    const ratio = info[0].match(/darker↔lighter=([\d.]+)/)?.[1] || 'N/A';
    const pass = parseFloat(ratio) >= 4.5;
    htmlReport += `<div class="contrast-pair ${pass ? 'pass' : 'fail'}">
      <div class="contrast-pair-label">darker ↔ lighter</div>
      <div class="contrast-pair-value">${ratio}:1</div>
      <div class="contrast-pair-status">${pass ? '✓ AA (4.5:1)' : '✗ Needs AA'}</div>
    </div>`;
  }

  if (palette.dark && palette.light) {
    const ratio = info[0].match(/dark↔light=([\d.]+)/)?.[1] || 'N/A';
    const pass = parseFloat(ratio) >= 3;
    htmlReport += `<div class="contrast-pair ${pass ? 'pass' : 'fail'}">
      <div class="contrast-pair-label">dark ↔ light</div>
      <div class="contrast-pair-value">${ratio}:1</div>
      <div class="contrast-pair-status">${pass ? '✓ AA Large (3:1)' : '✗ Needs AA Large'}</div>
    </div>`;
  }

  htmlReport += `</div></div>`;

  // Add comprehensive contrast grid
  const variantOrder = ["darkest", "darker", "dark", "mid", "light", "lighter", "lightest"];
  const availableVariants = variantOrder.filter(v => palette[v]);

  htmlReport += `
    <div class="contrast-matrix">
      <div class="contrast-matrix-title">📊 Full Contrast Matrix</div>
      <div class="matrix-description">Each cell shows the contrast ratio when using the row color as background and column color as foreground.</div>
      <div class="contrast-table-wrapper">
        <table class="contrast-table">
          <thead>
            <tr>
              <th class="corner-cell">BG → FG ↓</th>`;

  availableVariants.forEach(variant => {
    htmlReport += `<th class="variant-header">${variant}</th>`;
  });

  htmlReport += `</tr></thead><tbody>`;

  availableVariants.forEach(bgVariant => {
    htmlReport += `<tr><th class="variant-header">${bgVariant}</th>`;

    availableVariants.forEach(fgVariant => {
      if (bgVariant === fgVariant) {
        htmlReport += `<td class="same-color">—</td>`;
      } else {
        const bgColor = new Color(palette[bgVariant].hex);
        const fgColor = new Color(palette[fgVariant].hex);
        const ratio = getContrast(bgColor, fgColor);

        let badge = 'DNE';
        let badgeClass = 'dne';

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

        htmlReport += `
          <td class="contrast-cell">
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
        <div class="legend-item"><span class="legend-badge aaa">AAA</span> ≥7:1 (Best)</div>
        <div class="legend-item"><span class="legend-badge aa">AA</span> ≥4.5:1 (Normal text)</div>
        <div class="legend-item"><span class="legend-badge aa18">AA18</span> ≥3:1 (Large text 18pt+)</div>
        <div class="legend-item"><span class="legend-badge dne">DNE</span> &lt;3:1 (Does Not Meet)</div>
      </div>
    </div>
  </div>`;

  const allPasses = [...validation.accessibility.passes, ...validation.gamut.passes, ...validation.perceptual.passes];
  if (allPasses.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">✅ Passed Checks</div><ul class="validation-list">`;
    allPasses.forEach(pass => htmlReport += `<li class="validation-item pass">${pass}</li>`);
    htmlReport += `</ul></div>`;
  }

  const allIssues = [...validation.accessibility.issues, ...validation.gamut.issues, ...validation.perceptual.issues];
  if (allIssues.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">⚠️ Issues Found</div><ul class="validation-list">`;
    allIssues.forEach(issue => {
      const issueClass = issue.includes('✗') ? 'issue' : issue.includes('⚠') ? 'warning' : 'info';
      htmlReport += `<li class="validation-item ${issueClass}">${issue}</li>`;
    });
    htmlReport += `</ul></div>`;
  }

  if (validation.recommendations.length > 0) {
    htmlReport += `<div class="validation-section"><div class="validation-title">💡 Recommendations</div><ul class="validation-list">`;
    validation.recommendations.forEach(rec => htmlReport += `<li class="validation-item recommendation">${rec}</li>`);
    htmlReport += `</ul></div>`;
  }

  htmlReport += `</div>`;
});

htmlReport += `<div class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">↑</div>
    <footer>Generated by OKLCH Palette Generator with Validation & Scoring<br><strong>${totalPalettes}</strong> palettes analyzed • Average quality score: <strong>${avgScore}/100</strong></footer>
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
  </script>
</body>
</html>`;

fs.writeFileSync("./palette-report.html", htmlReport);

console.log("=".repeat(80));
console.log("✅ Enhanced color tokens with validation generated successfully!");
console.log("\n📁 Output files:");
console.log("   • ./src/styles/_tokens.generated.scss");
console.log("   • ./src/styles/tokens-generated.css");
console.log("   • ./palette-report.html (📊 View validation results)");
console.log("\n📊 Report Summary:");
console.log(`   • Total Palettes: ${totalPalettes}`);
console.log(`   • Average Score: ${avgScore}/100`);
console.log(`   • Excellent (≥95): ${excellentCount}`);
console.log(`   • Total Issues: ${issuesCount}`);
console.log();
