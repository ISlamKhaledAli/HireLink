<template>
  <main class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="text-xl font-semibold text-neutral-900">{{ heading }}</h1>
    <p class="mt-2 text-neutral-600">
      Employer dashboard screens are implemented in later phases; navigation is wired.
    </p>
  </main>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const heading = computed(() => route.meta.title || 'Employer')
</script>
