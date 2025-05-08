// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  ssr: true,
  devtools: { enabled: true },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxt/image',
  ],
  app: {
    head: {
      title: 'NutsShop - Магазин горішків та кондитерських виробів',
      meta: [
        { name: 'description', content: 'Найкращі горішки та кондитерські вироби в одному місці' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { charset: 'utf-8' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    },
    baseURL: '/'
  },
  server: {
    host: '0.0.0.0',
    port: 3000
  },
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8090',
    }
  },
  typescript: {
    strict: true,
    typeCheck: true
  },
  tailwindcss: {
    cssPath: '~/assets/css/tailwind.css',
    configPath: '~/tailwind.config.ts',
  },
  nitro: {
    compatibilityDate: '2025-05-07'
  }
}) 