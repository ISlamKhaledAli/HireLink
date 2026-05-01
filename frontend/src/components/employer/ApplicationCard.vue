<script setup>
defineProps({
  /** Raw application row from GET /applications */
  application: {
    type: Object,
    required: true
  }
})

function statusBadgeClass(status) {
  const map = {
    pending: 'border-amber-200 bg-amber-50 text-amber-900',
    accepted: 'border-emerald-200 bg-emerald-50 text-emerald-900',
    rejected: 'border-red-200 bg-red-50 text-red-900'
  }
  return map[status] || 'border-slate-200 bg-slate-50 text-slate-800'
}

function displayName(application) {
  const c = application.candidate
  if (c?.name) return c.name
  if (typeof c === 'object' && c?.full_name) return c.full_name
  return 'Candidate'
}
</script>

<template>
  <article
    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
    :aria-labelledby="`app-${application.id}-name`"
  >
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div class="min-w-0 flex-1">
        <h2
          :id="`app-${application.id}-name`"
          class="text-base font-black text-slate-900 truncate"
        >
          {{ displayName(application) }}
        </h2>
        <p class="mt-1 text-sm font-semibold text-slate-600 break-all">
          {{ application.email }}
        </p>
        <p v-if="application.phone" class="mt-1 text-sm font-semibold text-slate-500">
          {{ application.phone }}
        </p>
      </div>
      <span
        class="shrink-0 inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-black capitalize"
        :class="statusBadgeClass(application.status)"
      >
        {{ application.status }}
      </span>
    </div>
    <div v-if="$slots.actions" class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
      <slot name="actions" />
    </div>
  </article>
</template>
