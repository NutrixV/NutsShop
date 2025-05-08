<template>
  <img
    :src="imageUrl"
    :alt="alt || ''"
    :class="imgClass"
    @error="handleImageError"
    :style="imgStyle"
  />
</template>

<script setup>
import { computed, ref } from 'vue';
import { useConfig } from '@/composables/useConfig';

const { apiBaseUrl } = useConfig();

const props = defineProps({
  path: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  fallback: {
    type: String,
    default: '/placeholder-image.png'
  },
  imgClass: {
    type: [String, Object, Array],
    default: ''
  },
  imgStyle: {
    type: Object,
    default: () => ({})
  }
});

const hasError = ref(false);

const imageUrl = computed(() => {
  if (hasError.value) {
    return props.fallback;
  }
  // Use the image-proxy.php script
  return `${apiBaseUrl}/image-proxy.php?path=${encodeURIComponent(props.path)}`;
});

const handleImageError = () => {
  hasError.value = true;
};
</script> 