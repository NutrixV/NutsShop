import { useRuntimeConfig } from '#app';

export function useConfig() {
  const runtimeConfig = useRuntimeConfig();
  
  return {
    apiBaseUrl: runtimeConfig.public.apiBaseUrl || 'http://localhost:8090/api'
  };
} 