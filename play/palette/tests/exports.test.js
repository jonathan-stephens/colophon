// tests/exports.test.js
import test from "node:test";
import assert from "node:assert/strict";
import { PaletteCore } from "../src/palette-core.js";
import * as Exports from "../src/exports.js";

function makePalette() {
  const core = new PaletteCore({
    bases: [{ id: "base-1", label: "Primary", hex: "#3366ff" }],
    gridSize: 3,
    anchors: { white: "#ffffff", black: "#000000" },
  });
  core.generatePalette();
  return core.palette;
}

test("CSS export contains hex in comments", () => {
  const css = Exports.exportCSS(makePalette());
  assert.match(css, /#([0-9a-fA-F]{6})/);
});

test("SVG export includes data-color-hex attribute", () => {
  const svg = Exports.exportSVG(makePalette());
  assert.match(svg, /data-color-hex="#[0-9a-fA-F]{6}"/);
});

test("CSV export has hex column filled", () => {
  const csv = Exports.exportCSV(makePalette());
  const lines = csv.split("\n");
  const header = lines[0].split(",");
  const hexIndex = header.indexOf("hex");
  assert.notEqual(hexIndex, -1, "CSV must include hex column");

  const firstRow = lines[1].split(",");
  assert.match(firstRow[hexIndex], /^#[0-9a-fA-F]{6}$/);
});

test("Pair CSV export includes both foreground and background hex", () => {
  const csv = Exports.exportCSVPairs(makePalette());
  const lines = csv.split("\n");
  assert.match(lines[0], /foreground_hex,background_hex/);
  assert.match(lines[1], /#[0-9a-fA-F]{6},#[0-9a-fA-F]{6}/);
});
