/// <reference types="vite/client" />

interface ImportMetaEnv {
  NODE_ENV: string
  VITE_API_URL: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
} 