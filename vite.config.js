import {fileURLToPath, URL} from 'node:url'

import legacy from '@vitejs/plugin-legacy'
import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    legacy({
      targets: ['defaults']
    }),
    VueI18nPlugin({
      include: [path.resolve(__dirname, './client/locales/**')],
    }),
  ],
  resolve: {
    alias: {
      'vue-i18n': 'vue-i18n/dist/vue-i18n.runtime.esm-bundler.js', // This is required for a CSP compliant build.
      '@': fileURLToPath(new URL('./client', import.meta.url))
    }
  },

  root: 'client',

  build: {
    // output dir for production build
    outDir: '../public',

    // our entry
    rollupOptions: {
      input: path.resolve(__dirname, 'client/index.html'),
    }
  },

  server: {
    strictPort: true,
    port: 5173
  },
})
