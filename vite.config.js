import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import liveReload from "vite-plugin-live-reload";
import sass from "sass";
import { resolve } from "path";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react(), liveReload(__dirname + "/**/*.php")],
  server: {
    host: "0.0.0.0",
    cors: true,
  },
  css: {
    preprocessorOptions: {
      scss: {
        implementation: sass,
      },
    },
  },
  build: {
    // output dir for production build
    outDir: resolve(__dirname, "./dist"),
    emptyOutDir: false,

    // emit manifest so PHP can find the hashed files
    manifest: true,

    // esbuild target
    target: "es2018",

    // our entry
    rollupOptions: {
      input: {
        main: resolve(__dirname + "/src/main.jsx"),
      },
      output: {
        entryFileNames: "assets/js/[name].js",
        chunkFileNames: "assets/js/[name].js",
        assetFileNames: "assets/[ext]/[name].[ext]",
      },
    },


    // minifying switch
    minify: true,
    write: true,
  },
  resolve: {
    alias: {
      "@": "/src",
      "@js": "/src/assets/js",
      "@components": "/src/components",
      "@scss": "/src/assets/scss",
    },
  },
});
