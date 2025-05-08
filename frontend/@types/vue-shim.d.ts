declare module 'vue' {
  export function computed<T>(getter: () => T): { readonly value: T };
  export function computed<T>(options: { get: () => T; set: (value: T) => void }): { value: T };
  export function onMounted(callback: () => any): void;
  export function watch<T>(source: T, callback: (newValue: T, oldValue: T) => void, options?: object): () => void;
}

declare module 'vue-router' {
  export function useRoute(): any;
  export function useRouter(): any;
} 