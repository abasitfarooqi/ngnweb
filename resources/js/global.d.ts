// resources/js/global.d.ts

// Declare global types here
declare module '*.js' {
    const content: any
    export default content
  }
  
  // Extend Window interface if needed
  declare interface Window {
    // Add any global window properties here
  }
  
  // Extend NodeJS namespace if needed
  declare namespace NodeJS {
    interface ProcessEnv {
      // Add any environment variables here
    }
  } 