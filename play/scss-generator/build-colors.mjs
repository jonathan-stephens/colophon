// build-colors.mjs (Refined OKLCH palette generator v5 - chroma corrected + semantic top normalization)
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

// === Hue/chroma utilities ===
function getMaxChromaForHue(h, l) {
  const hueNorm = ((h || 0) % 360) / 360;
  const hueFactor = Math.sin((hueNorm - 0.167) * Math.PI * 2) * 0.3 + 1;
  const lightnessFactor = 1 - Math.pow(l, 1.2);
  return 0.37 * hueFactor * lightnessFactor;
}

function sigmoid(t) {
  // simple smooth curve between 0 and 1, centered at 0.5
  return 1 / (1 + Math.exp(-10 * (t - 0.5)));
}

function adaptChromaHue(baseColor, targetL, params, dirSign = 1) {
  const { l: bL, c: bC, h: bH } = baseColor.oklch;
  const dist = Math.abs(targetL - bL);

  // Compute chroma modulation using sigmoid shape
  const t = clamp((targetL - 0.2) / 0.6, 0, 1);
  const chromaFactor = 0.6 + 0.7 * sigmoid(t); // stays within ~0.6–1.3
  const dirFactor = dirSign < 0 ? 0.9 : 1.05;

  let c = bC * chromaFactor * dirFactor;
  c = clamp(c, 0.015, getMaxChromaForHue(bH, targetL));

  const rot = dirSign * params.hueAdjustment.maxRotationDeg * Math.pow(dist, 1.1);
  const h = (bH + rot) % 360;

  return { c, h };
}

// === Contrast-aware lightness distribution ===
function distributeContrastAware(baseColor, variants, spread, contrastTargets) {
  const baseL = baseColor.oklch.l;
  let minL = baseL < 0.5 ? 0.03 : baseL - 0.45;
  let maxL = baseL > 0.5 ? 0.97 : baseL + 0.45;
  minL = clamp(minL, 0.03, 0.4);
  maxL = clamp(maxL, 0.6, 0.98);

  // Expand range until AAA contrast
  let darkest = createColorOKLCH(minL, baseColor.oklch.c, baseColor.oklch.h);
  let lightest = createColorOKLCH(maxL, baseColor.oklch.c, baseColor.oklch.h);
  let attempts = 0;
  while (getContrast(darkest, lightest) < contrastTargets.AAA && attempts < 20) {
    minL = Math.max(0.01, minL - 0.015);
    maxL = Math.min(0.99, maxL + 0.01);
    darkest = createColorOKLCH(minL, baseColor.oklch.c, baseColor.oklch.h);
    lightest = createColorOKLCH(maxL, baseColor.oklch.c, baseColor.oklch.h);
    attempts++;
  }

  // Smooth distribution
  const n = variants.length;
  const range = maxL - minL;
  const pos = {};
  for (let i = 0; i < n; i++) {
    const t = i / (n - 1);
    const eased = Math.pow(t, 0.9);
    pos[variants[i]] = clamp(minL + range * eased, 0.02, 0.98);
  }
  pos["mid"] = baseL;
  return pos;
}

// === Normalize semantic order & taper chroma ===
function normalizeSemanticOrder(colorsObj) {
  const orderedNames = [
    "darkest",
    "darker",
    "dark",
    "mid",
    "light",
    "lighter",
    "lightest",
  ];

  // sort variants by lightness
  const sorted = Object.entries(colorsObj)
    .map(([k, c]) => ({ key: k, color: c }))
    .sort((a, b) => a.color.oklch.l - b.color.oklch.l);

  const normalized = {};
  for (let i = 0; i < orderedNames.length; i++) {
    const src = sorted[i] ?? sorted[sorted.length - 1];
    normalized[orderedNames[i]] = src.color;
  }

  // Enforce monotonic spacing
  const minDelta = 0.04;
  for (let i = 1; i < orderedNames.length; i++) {
    const prev = normalized[orderedNames[i - 1]];
    const curr = normalized[orderedNames[i]];
    if (curr.oklch.l - prev.oklch.l < minDelta) {
      const newL = clamp(prev.oklch.l + minDelta, 0.02, 0.98);
      normalized[orderedNames[i]] = createColorOKLCH(
        newL,
        curr.oklch.c,
        curr.oklch.h
      );
    }
  }

  // Smooth chroma taper toward both ends
  const Lmin = normalized.darkest.oklch.l;
  const Lmax = normalized.lightest.oklch.l;
  for (const key of orderedNames) {
    const c = normalized[key];
    const rel = (c.oklch.l - Lmin) / (Lmax - Lmin);
    const fade = 0.8 - 0.4 * Math.cos(rel * Math.PI); // keeps color mid strong, fades near edges
    const newC = clamp(c.oklch.c * fade, 0.01, 0.35);
    normalized[key] = createColorOKLCH(c.oklch.l, newC, c.oklch.h);
  }

  // Recompute mid as perceptual midpoint
  const midL = (Lmin + Lmax) / 2;
  const midC = normalized.mid.oklch.c;
  const midH = normalized.mid.oklch.h;
  normalized.mid = createColorOKLCH(midL, midC, midH);

  return normalized;
}

// === Main palette generation ===
function generatePalette(name, rawEntry, cfg) {
  const entry = typeof rawEntry === "string" ? { base: rawEntry } : { ...rawEntry };
  const baseColor = new Color(entry.base);
  const baseL = baseColor.oklch.l;
  const { variants, spread, contrastTargets } = cfg.settings;

  const spectrum = distributeContrastAware(baseColor, variants, spread, contrastTargets);

  const colorsObj = {};
  for (const [variant, L] of Object.entries(spectrum)) {
    const sign = L < baseL ? -1 : 1;
    const { c, h } = adaptChromaHue(baseColor, L, cfg.settings, sign);
    colorsObj[variant] = createColorOKLCH(L, c, h);
  }
  colorsObj.base = baseColor;

  // Normalize and taper
  const normalized = normalizeSemanticOrder(colorsObj);

  const palette = {};
  for (const [variant, col] of Object.entries(normalized)) {
    palette[variant] = {
      oklch: roundOklch(toOklch(col)),
      hex: toHex(col),
      rgb: toRgb(col),
    };
  }

  // also include base
  palette.base = {
    oklch: roundOklch(toOklch(baseColor)),
    hex: toHex(baseColor),
    rgb: toRgb(baseColor),
  };

  const lightest = normalized.lightest;
  const darkest = normalized.darkest;
  const mid = normalized.mid;

  const aaa = getContrast(darkest, lightest).toFixed(2);
  const aa1 = getContrast(mid, lightest).toFixed(2);
  const aa2 = getContrast(mid, darkest).toFixed(2);

  const warnings = [];
  if (aaa < 7) warnings.push(`❌ AAA not met (darkest↔lightest=${aaa})`);
  if (aa1 < 4.5) warnings.push(`⚠️ mid↔lightest < 4.5 (${aa1})`);
  if (aa2 < 4.5) warnings.push(`⚠️ mid↔darkest < 4.5 (${aa2})`);

  return { name, palette, baseL, warnings };
}

// ==========================================================
const colors = JSON.parse(fs.readFileSync("./colors.config.json", "utf8"));

const config = {
  settings: {
    variants: [
      "darkest",
      "darker",
      "dark",
      "mid",
      "light",
      "lighter",
      "lightest",
    ],
    spread: { strength: 1.0 },
    hueAdjustment: { maxRotationDeg: 6 },
    contrastTargets: { AAA: 7, AA: 4.5 },
  },
};

let scssOutput = `// Auto-generated OKLCH tokens (Refined semantic v5)\n:root {\n`;
let cssOutput = `/* Auto-generated OKLCH tokens (Refined semantic v5) */\n:root {\n`;

console.log("\n🎨 Generating perceptually ordered, contrast-safe palettes...\n");

for (const [name, base] of Object.entries(colors)) {
  const result = generatePalette(name, base, config);
  const { palette, baseL, warnings } = result;

  scssOutput += `  // ${name.toUpperCase()} - Ordered Spectrum\n`;
  cssOutput += `  /* ${name.toUpperCase()} - Ordered Spectrum */\n`;

  for (const [variant, data] of Object.entries(palette)) {
    scssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
    cssOutput += `  --${name}-${variant}: ${data.oklch};\n`;
  }

  scssOutput += "\n";
  cssOutput += "\n";

  console.log(`✓ ${name}: base L=${baseL.toFixed(3)} | darkest=${palette.darkest.oklch} | lightest=${palette.lightest.oklch}`);
  if (warnings.length) warnings.forEach((w) => console.log("  " + w));
}

scssOutput += "}\n";
cssOutput += "}\n";

fs.writeFileSync("./src/styles/_tokens.generated.scss", scssOutput);
fs.writeFileSync("./src/styles/tokens-generated.css", cssOutput);

console.log("\n✅ Color tokens generated successfully!");
