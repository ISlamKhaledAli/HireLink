<script setup>
import { computed } from 'vue'
import { Loader2 } from 'lucide-vue-next'
import { useApplicationsStore } from '@/stores/applications'

const props = defineProps({
  /** Raw application row from GET /applications */
  application: {
    type: Object,
    required: true
  }
})

const applicationsStore = useApplicationsStore()

const updating = computed(() => applicationsStore.isUpdating(props.application.id))
const statusError = computed(() => applicationsStore.statusErrorFor(props.application.id))

function accept() {
  applicationsStore.setStatus(props.application.id, 'accepted')
}

function reject() {
  applicationsStore.setStatus(props.application.id, 'rejected')
}

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

    <div
      v-if="application.status === 'pending'"
      class="mt-4 border-t border-slate-100 pt-4"
      :aria-busy="updating"
    >
      <p
        v-if="statusError"
        class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-800"
        role="alert"
      >
        {{ statusError }}
      </p>
      <div class="flex flex-wrap items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-600 px-4 py-2 text-sm font-black text-white shadow-sm hover:bg-emerald-700 disabled:pointer-events-none disabled:opacity-50"
          :disabled="updating"
          @click="accept"
        >
          Accept
        </button>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-black text-red-700 shadow-sm hover:bg-red-50 disabled:pointer-events-none disabled:opacity-50"
          :disabled="updating"
          @click="reject"
        >
          Reject
        </button>
        <Loader2
          v-if="updating"
          class="h-5 w-5 animate-spin text-primary"
          aria-label="Updating status"
        />
      </div>
    </div>
  </article>
</template>
