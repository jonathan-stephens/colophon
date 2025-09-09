// central constants and grid definitions
export const GRID_MODES = {
  "3": ["darkest", "core", "lightest"],
  "5": ["darkest", "darker", "core", "lighter", "lightest"],
  "7": ["darkest", "darker", "dark", "core", "light", "lighter", "lightest"]
};

export const DEFAULT_BASES = [
  { id: "base-1", label: "Base 1", hex: "#0F3460" },
  { id: "base-2", label: "Base 2", hex: "#533483" },
  { id: "base-3", label: "Base 3", hex: "#1a1a2e" }
];

// default lightness offsets & chroma factors for 3/5/7 (relative to "center" position)
export const VARIANT_PRESETS = {
  "3": {
    offsets: [-0.42, 0, 0.85],
    chroma: [1.2, 1.0, 0.7]
  },
  "5": {
    offsets: [-0.35, -0.15, 0, 0.15, 0.35],
    chroma: [1.4, 1.2, 1.0, 0.8, 0.6]
  },
  "7": {
    offsets: [-0.39, -0.26, -0.13, 0, 0.13, 0.26, 0.39],
    chroma: [1.3, 1.2, 1.1, 1.0, 0.9, 0.8, 0.7]
  }
};
