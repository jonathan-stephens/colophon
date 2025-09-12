// vite.config.js
import { defineConfig } from "vite";

export default defineConfig({
  base: "./", // ✅ makes built JS/CSS relative instead of root-absolute
  build: {
    outDir: "dist",
    rollupOptions: {
      output: {
        manualChunks: undefined, // forces everything into one bundle
      },
    },
  },
});
