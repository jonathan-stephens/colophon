// tests/palette-core.test.js
import test from "node:test";
import assert from "node:assert/strict";
import { PaletteCore } from "../src/palette-core.js";

test("PaletteCore generates 3x3 grid with correct variant names", () => {
  const core = new PaletteCore({
    bases: [{ id: "base-1", label: "Primary", hex: "#3366ff" }],
    gridSize: 3,
    anchors: { white: "#ffffff", black: "#000000" },
  });
  core.generatePalette();
  const variants = Object.keys(core.palette["base-1"].variants);
  assert.deepEqual(variants, ["darker", "core", "lighter"]);
});

test("PaletteCore generates 5x5 grid with darkest/lightest included", () => {
  const core = new PaletteCore({
    bases: [{ id: "base-1", label: "Primary", hex: "#3366ff" }],
    gridSize: 5,
    anchors: { white: "#ffffff", black: "#000000" },
  });
  core.generatePalette();
  const variants = Object.keys(core.palette["base-1"].variants);
  assert.deepEqual(variants, ["darkest", "darker", "core", "lighter", "lightest"]);
});

test("Anchors are respected when generating palette", () => {
  const core = new PaletteCore({
    bases: [{ id: "base-1", label: "Primary", hex: "#888888" }],
    gridSize: 3,
    anchors: { white: "#fdf7f1", black: "#111820" },
  });
  core.generatePalette();
  const base = core.palette["base-1"];
  assert.equal(base.base.label, "Primary");
  assert.ok(Object.values(base.variants).every(v => v.hex.startsWith("#")));
});
