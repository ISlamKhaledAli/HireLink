<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const jobId = computed(() => route.params.jobId)
</script>

<template>
  <main class="mx-auto max-w-3xl px-4 py-8 font-body">
    <h1 class="text-xl font-semibold text-slate-900">Applications</h1>
    <p class="mt-2 text-sm font-medium text-slate-600">
      Job ID {{ jobId }} — candidate applications will be listed here in a later step.
    </p>
    <router-link
      :to="{ name: 'employer-jobs' }"
      class="mt-6 inline-block text-sm font-bold text-primary hover:underline"
    >
      ← Back to my jobs
    </router-link>
  </main>
</template>
