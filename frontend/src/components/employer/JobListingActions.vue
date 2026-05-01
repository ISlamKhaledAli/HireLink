<script setup>
defineProps({
  jobId: {
    type: [Number, String],
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

defineEmits(['edit', 'delete'])
</script>

<template>
  <div class="flex flex-wrap items-center gap-2">
    <button
      type="button"
      class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:border-primary hover:text-primary disabled:opacity-45 disabled:pointer-events-none transition-colors"
      :disabled="disabled"
      @click="$emit('edit')"
    >
      Edit
    </button>
    <button
      type="button"
      class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-700 shadow-sm hover:bg-red-50 disabled:opacity-45 disabled:pointer-events-none transition-colors"
      :disabled="disabled"
      @click="$emit('delete')"
    >
      Delete
    </button>
    <router-link
      :to="{ name: 'employer-job-applications', params: { jobId } }"
      class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-primary shadow-sm hover:border-primary hover:bg-primary/5 transition-colors"
    >
      Applications
    </router-link>
  </div>
</template>
