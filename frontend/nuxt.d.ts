/// <reference types="@types/node" />

// Оголошення глобальних функцій Nuxt
declare function defineNuxtConfig(config: any): any;

// Оголошення типу для Vue
declare module 'vue' {
  export function ref<T>(value: T): { value: T };
  export function onMounted(cb: () => void): void;
}

// Оголошення типу для useRuntimeConfig
declare module '#app' {
  export function useRuntimeConfig(): {
    public: {
      apiBaseUrl: string;
    };
  };
}

// Оголошення глобальних змінних Node.js
declare namespace NodeJS {
  interface ProcessEnv {
    API_URL?: string;
  }
}

declare var process: NodeJS.Process; 