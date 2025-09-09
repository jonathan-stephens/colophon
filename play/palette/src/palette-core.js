// src/palette-core.js
import { parseToColor, toHex } from "./color-utils.js";

export class PaletteCore {
  constructor({ bases = [], gridSize = "5", anchors = { white: "#fff", black: "#000" } }) {
    this.bases = bases;
    this.gridSize = gridSize;
    this.anchors = anchors;
    this.palette = {};
  }

  setBases(bases) {
    this.bases = bases;
  }

  setGridSize(size) {
    this.gridSize = size;
  }

  setAnchors(anchors) {
    this.anchors = anchors;
  }

  generatePalette() {
    const result = {};

    this.bases.forEach(base => {
      const parsedBase = parseToColor(base.hex);
      if (!parsedBase) return;

      const variants = {};
      const steps = this._getSteps(this.gridSize);

      steps.forEach((lightness, i) => {
        const variantName = this._variantName(i, steps.length);
        const adjusted = parsedBase.to("oklch");
        adjusted.l = lightness;

        const parsedVariant = parseToColor(adjusted.toString());
        if (!parsedVariant) return;

        const hex = toHex(parsedVariant);

        variants[variantName] = {
          id: `${base.id}-${variantName}`,
          label: `${base.label} ${variantName}`,
          hex,
          color: parsedVariant, // 👈 ensure ui.js gets the parsed color
        };
      });

      result[base.id] = { base, variants };
    });

    this.palette = result;
    return result;
  }

  // For 3/5/7 grid modes — evenly spaced lightness anchors
  _getSteps(size) {
    switch (size.toString()) {
      case "3":
        return [0.35, 0.57, 0.78]; // darker, core, lighter
      case "5":
        return [0.2, 0.35, 0.57, 0.75, 0.9]; // darkest → lightest
      case "7":
        return [0.1, 0.2, 0.35, 0.57, 0.75, 0.9, 0.98];
      default:
        return [0.35, 0.57, 0.78];
    }
  }

  _variantName(index, total) {
    if (total === 3) return ["darker", "core", "lighter"][index];
    if (total === 5) return ["darkest", "darker", "core", "lighter", "lightest"][index];
    if (total === 7)
      return ["darkest", "darker", "dark", "core", "light", "lighter", "lightest"][index];
    return `v${index}`;
  }
}
