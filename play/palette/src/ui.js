// src/ui.js
import { PaletteCore } from "./palette-core.js";
import * as Exports from "./exports.js";
import {
  randomHex,
  parseToColor,
  toHex,
  toRGBA,
  toOKLCH,
  toOKLAB,
} from "./color-utils.js";

const STORAGE_KEY = "palette-generator-state-v6";

function showToast(msg) {
  const el = document.getElementById("toast");
  el.textContent = msg;
  el.classList.add("visible");
  setTimeout(() => el.classList.remove("visible"), 1800);
}

export class UI {
  constructor() {
    this.state = {
      gridSize: "5",
      bases: [
        { id: "base-1", label: "Primary", hex: "#0F3460" },
        { id: "base-2", label: "Accent", hex: "#E94560" },
      ],
      anchors: { white: "#ffffff", black: "#000000" },
      showSpaces: { hex: true, rgba: false, oklch: true, oklab: false },
    };

    this._loadState();
    this.paletteCore = new PaletteCore({
      bases: this.state.bases,
      gridSize: this.state.gridSize,
      anchors: this.state.anchors,
    });

    this._bindControls();
    this._renderBases();
    this._renderAll();
  }

  _saveState() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state));
  }
  _loadState() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) Object.assign(this.state, JSON.parse(raw));
  }

  _bindControls() {
    // Grid size
    const gridSizeSel = document.getElementById("gridSize");
    gridSizeSel.value = this.state.gridSize;
    gridSizeSel.addEventListener("change", e => {
      this.state.gridSize = e.target.value;
      this._saveState();
      this._renderAll();
    });

    // Anchors
    ["White", "Black"].forEach(anchor => {
      const textEl = document.getElementById(`anchor${anchor}Text`);
      const colorEl = document.getElementById(`anchor${anchor}Color`);
      textEl.value = this.state.anchors[anchor.toLowerCase()];
      colorEl.value = this.state.anchors[anchor.toLowerCase()];

      textEl.addEventListener("change", e => {
        const c = parseToColor(e.target.value);
        if (c) {
          const hex = toHex(c);
          this.state.anchors[anchor.toLowerCase()] = hex;
          colorEl.value = hex;
          this._saveState();
          this._renderAll();
        }
      });
      colorEl.addEventListener("input", e => {
        this.state.anchors[anchor.toLowerCase()] = e.target.value;
        textEl.value = e.target.value;
        this._saveState();
        this._renderAll();
      });
    });

    // Show spaces toggles
    document.querySelectorAll("input[type=checkbox][data-space]").forEach(cb => {
      cb.checked = this.state.showSpaces[cb.dataset.space];
      cb.addEventListener("change", e => {
        this.state.showSpaces[e.target.dataset.space] = e.target.checked;
        this._saveState();
        this._renderAll();
      });
    });

    // Add Base
    document.getElementById("addBaseBtn").addEventListener("click", () => {
      const id = `base-${Date.now()}`;
      this.state.bases.push({
        id,
        label: `Base ${this.state.bases.length + 1}`,
        hex: randomHex(),
      });
      this._saveState();
      this._renderBases();
      this._renderAll();
    });

    // Randomize
    document.getElementById("randomizeBtn").addEventListener("click", () => {
      this.state.bases = this.state.bases.map(b => ({ ...b, hex: randomHex() }));
      this._saveState();
      this._renderBases();
      this._renderAll();
    });

    // Export buttons
    document.getElementById("downloadCssBtn").addEventListener("click", () => {
      Exports.downloadCSS(this.paletteCore.palette);
    });
    document.getElementById("downloadSvgBtn").addEventListener("click", () => {
      Exports.downloadSVG(this.paletteCore.palette);
    });
    document.getElementById("downloadCsvBtn").addEventListener("click", () => {
      Exports.downloadCSV(this.paletteCore.palette);
    });
    document.getElementById("downloadCsvPairsBtn").addEventListener("click", () => {
      Exports.downloadCSVPairs(this.paletteCore.palette);
    });

    // Copy CSS
    document.getElementById("copyCssBtn").addEventListener("click", () => {
      const css = Exports.exportCSS(this.paletteCore.palette);
      navigator.clipboard.writeText(css);
      showToast("✅ CSS copied");
    });
  }

  _renderBases() {
    const container = document.getElementById("basesContainer");
    container.innerHTML = "";
    this.state.bases.forEach(b => {
      const item = document.createElement("div");
      item.className = "base-item";

      const colorInput = document.createElement("input");
      colorInput.type = "color";
      colorInput.value = b.hex;
      colorInput.addEventListener("input", e => this._updateBaseHex(b.id, e.target.value));

      const textInput = document.createElement("input");
      textInput.type = "text";
      textInput.className = "color-text";
      textInput.value = b.hex;
      textInput.addEventListener("change", e => {
        const c = parseToColor(e.target.value);
        if (c) this._updateBaseHex(b.id, toHex(c));
      });

      const labelInput = document.createElement("input");
      labelInput.type = "text";
      labelInput.value = b.label;
      labelInput.className = "base-item__label";
      labelInput.addEventListener("change", e => this._updateBaseLabel(b.id, e.target.value));

      const removeBtn = document.createElement("button");
      removeBtn.textContent = "Remove";
      removeBtn.addEventListener("click", () => {
        this.state.bases = this.state.bases.filter(x => x.id !== b.id);
        this._saveState();
        this._renderBases();
        this._renderAll();
      });

      item.append(colorInput, textInput, labelInput, removeBtn);
      container.appendChild(item);
    });
  }

  _updateBaseHex(id, hex) {
    this.state.bases = this.state.bases.map(b => b.id === id ? { ...b, hex } : b);
    this._saveState();
    this._renderAll();
  }
  _updateBaseLabel(id, label) {
    this.state.bases = this.state.bases.map(b => b.id === id ? { ...b, label } : b);
    this._saveState();
    this._renderAll();
  }

  _renderAll() {
    this.paletteCore.setGridSize(this.state.gridSize);
    this.paletteCore.setBases(this.state.bases);
    this.paletteCore.setAnchors(this.state.anchors);
    this.paletteCore.generatePalette();
    this._renderPaletteGrid();
    this._renderCSSOutput();
  }

  _renderPaletteGrid() {
    const container = document.getElementById("paletteGrid");
    container.innerHTML = "";
    const palette = this.paletteCore.palette;

    Object.values(palette).forEach(({ base, variants }) => {
      const row = document.createElement("div");
      row.className = "palette__row";

      const label = document.createElement("div");
      label.className = "u-cap";
      label.textContent = base.label;

      const cells = document.createElement("ul");
      cells.className = "palette__cells";

      Object.values(variants).forEach(v => {
        const li = document.createElement("li");
        li.className = "swatch";
        li.style.background = v.hex;

        // Build meta info based on enabled spaces
        let metaParts = [];
        if (this.state.showSpaces.hex) metaParts.push(v.hex);
        if (this.state.showSpaces.rgba) metaParts.push(toRGBA(v.color));
        if (this.state.showSpaces.oklch) metaParts.push(toOKLCH(v.color));
        if (this.state.showSpaces.oklab) metaParts.push(toOKLAB(v.color));
        if (this.state.showSpaces.oklch && v.color) metaParts.push(toOKLCH(v.color));

        li.innerHTML = `<div class="swatch__meta">${metaParts.join(" | ")}</div>`;
        li.addEventListener("click", () => {
          navigator.clipboard.writeText(v.hex);
          showToast(`🎨 ${v.hex} copied`);
        });
        cells.appendChild(li);
      });

      row.append(label, cells);
      container.appendChild(row);
    });
  }

  _renderCSSOutput() {
    const el = document.getElementById("cssOutput");
    el.textContent = Exports.exportCSS(this.paletteCore.palette);
  }
}
