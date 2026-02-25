// resources/js/shims-vue.d.ts
// Vue Components
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

// Images
declare module '*.png' {
  const value: string
  export default value
}

declare module '*.jpg' {
  const value: string
  export default value
}

declare module '*.jpeg' {
  const value: string
  export default value
}

declare module '*.gif' {
  const value: string
  export default value
}

declare module '*.svg' {
  const value: string
  export default value
}

// Media
declare module '*.mp4' {
  const value: string
  export default value
}

// Fonts
declare module '*.woff' {
  const value: string
  export default value
}

declare module '*.woff2' {
  const value: string
  export default value
}

declare module '*.eot' {
  const value: string
  export default value
}

declare module '*.ttf' {
  const value: string
  export default value
}

// JSON
declare module '*.json' {
  const value: any
  export default value
} 