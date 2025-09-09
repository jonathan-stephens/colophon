// src/color-utils.js
import Color from "colorjs.io";

/**
 * Safely clamp a value between min and max
 */
function clamp(val, min, max) {
  return Math.min(Math.max(val, min), max);
}

/**
 * Parse input into a Color object
 * Supports hex, rgb(a), oklch, oklab, etc.
 */
export function parseToColor(input) {
  try {
    return new Color(input);
  } catch (err) {
    console.warn(`Invalid color input: ${input}`, err);
    // fallback to black
    return new Color("black");
  }
}

/**
 * Convert a Color object to HEX (#rrggbb)
 * Always returns valid 6-digit hex
 */
export function toHex(color) {
  const srgb = color.to("srgb").coords.map((c) =>
    Math.round(clamp(c, 0, 1) * 255)
  );
  const hex = `#${srgb
    .map((v) => v.toString(16).padStart(2, "0"))
    .join("")}`;
  return hex.toUpperCase();
}

/**
 * Convert a Color object to RGBA string
 */
export function toRGBA(color) {
  const [r, g, b] = color.to("srgb").coords.map((c) =>
    Math.round(clamp(c, 0, 1) * 255)
  );
  const a = clamp(color.alpha ?? 1, 0, 1);
  return `rgba(${r}, ${g}, ${b}, ${a})`;
}

/**
 * Convert a Color object to OKLCH string
 */
export function toOKLCH(color) {
  const [l, c, h] = color.to("oklch").coords;
  return `oklch(${l.toFixed(4)} ${c.toFixed(5)} ${h.toFixed(3)})`;
}

/**
 * Convert a Color object to OKLAB string
 */
export function toOKLAB(color) {
  const [l, a, b] = color.to("oklab").coords;
  return `oklab(${l.toFixed(4)} ${a.toFixed(5)} ${b.toFixed(5)})`;
}

/**
 * Generate a random hex color (#RRGGBB)
 */
export function randomHex() {
  const hex = "#" + Math.floor(Math.random() * 0xffffff)
    .toString(16)
    .padStart(6, "0");
  return hex.toUpperCase();
}
