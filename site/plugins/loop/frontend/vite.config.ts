/// <reference types="vitest" />

import { defineConfig, loadEnv } from "vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import cssInjectedByJsPlugin from "vite-plugin-css-injected-by-js";
import { ViteEjsPlugin } from 'vite-plugin-ejs'
import { browserslistToTargets } from 'lightningcss';
import browserslist from "browserslist"

// Isomorphic dirname
const _dirname =
  typeof __dirname !== "undefined"
    ? __dirname
    : dirname(fileURLToPath(import.meta.url));

// Config
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  return {
    base: env.VITE_DEMO_BASE,
    compilerOptions: {
      hmr: !process.env.VITEST && mode !== 'production',
    },
    build: {
      cssMinify: 'lightningcss',
      minify: true,
      lib: {
        entry: resolve(_dirname, "src/main.ts"),
        name: "Loop",
        fileName: "loop",
        formats: ["es"],
      },
      outDir: "../assets",
    },
    css: {
      transformer: 'lightningcss',
      lightningcss: {
        drafts: {
          customMedia: true
        },
        targets: browserslistToTargets(browserslist(["last 2 versions", ">= 0.4%", "not dead", "Firefox ESR", "not op_mini all", "not and_uc > 0"]))
      }
    },
    define: {
      APP_VERSION: JSON.stringify(process.env.npm_package_version),
    },
    plugins: [
      svelte({ compilerOptions: { customElement: true } }),
      cssInjectedByJsPlugin(),
      ViteEjsPlugin((viteConfig) => ({
        // viteConfig is the current Vite resolved config
        env: viteConfig.env,
      }))
    ],
    test: {
      globals: true,
      environment: "jsdom",
    },
    server: {
      allowedHosts: ['kirby-loop.test'],
      cors: {
        // Allow ddev and .test domains
        origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(\.(ddev\.site|test))(?::\d+)?$/,
      },
    }
  }
});
