import { useRuntimeConfig } from '#app';

export function useConfig() {
  const runtimeConfig = useRuntimeConfig();
  
  return {
    apiBaseUrl: runtimeConfig.public.apiBaseUrl ? `${runtimeConfig.public.apiBaseUrl}/api` : 'http://localhost:8090/api'
  };
} 