import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../dist/assets',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        admin: path.resolve(__dirname, 'src/main.jsx'),
      },
      output: {
        entryFileNames: '[name].js',
        assetFileNames: 'index.css',
      },
    },
  },
});
