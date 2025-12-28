import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import obfuscatorPlugin from "vite-plugin-bundle-obfuscator";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/livewire.js",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
        obfuscatorPlugin({
            autoExcludeNodeModules: true,
            threadPool: true,
            options: {
                compact: true,
                controlFlowFlattening: true,
                debugProtection: true,
                disableConsoleOutput: true,
                selfDefending: true,
            },
        }),
    ],
    build: {
        sourcemap: process.env.NODE_ENV !== "production",
        minify: "terser",
    },
});