import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000', // FastAPIサーバー
        changeOrigin: true,
        // rewrite: (path) => path.replace(/^\/api/, '/api'), // 通常不要
      },
    },
  },
})
