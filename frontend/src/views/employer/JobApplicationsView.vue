<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { Loader2 } from 'lucide-vue-next'
import { useApplicationsStore } from '@/stores/applications'
import { useJobStore } from '@/stores/jobStore'
import ApplicationCard from '@/components/employer/ApplicationCard.vue'

const route = useRoute()
const applicationsStore = useApplicationsStore()
const jobStore = useJobStore()

const { items, loading, error } = storeToRefs(applicationsStore)
const { myJobs } = storeToRefs(jobStore)

const jobIdParam = computed(() => route.params.jobId)

const numericJobId = computed(() => {
  const n = Number(jobIdParam.value)
  return Number.isFinite(n) ? n : null
})

const filteredApplications = computed(() => {
  const id = numericJobId.value
  if (id == null) return []
  return items.value.filter((a) => Number(a.job_id) === id)
})

const jobTitle = computed(() => {
  const id = numericJobId.value
  if (id == null) return null
  const fromApp = filteredApplications.value.find((a) => a.job)?.job?.title
  if (fromApp) return fromApp
  const row = myJobs.value.find((j) => Number(j.id) === id)
  return row?.title ?? null
})

const headingSubtitle = computed(() => {
  if (numericJobId.value == null) return null
  return jobTitle.value || `Job #${numericJobId.value}`
})

async function load() {
  await Promise.all([
    applicationsStore.fetchAll(),
    myJobs.value.length ? Promise.resolve() : jobStore.fetchMyJobs({ page: 1 })
  ])
}

onMounted(() => {
  load()
})
</script>

<template>
  <div class="bg-[#FAFAFC] min-h-screen font-body text-slate-900">
    <main class="mx-auto max-w-3xl px-4 py-8 sm:py-10">
      <router-link
        :to="{ name: 'employer-jobs' }"
        class="inline-flex text-sm font-black text-primary hover:underline mb-6"
      >
        ← Back to my jobs
      </router-link>

      <header class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">
          Applications
        </h1>
        <p v-if="headingSubtitle" class="mt-2 text-slate-600 font-semibold text-sm">
          For {{ headingSubtitle }}
        </p>
      </header>

      <div
        v-if="loading && !items.length"
        class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-6 text-slate-600 font-bold text-sm"
      >
        <Loader2 class="h-5 w-5 animate-spin text-primary" aria-hidden="true" />
        Loading applications…
      </div>

      <div
        v-else-if="error"
        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-semibold text-sm"
      >
        {{ error }}
        <button
          type="button"
          class="mt-3 font-black text-primary underline block"
          @click="load()"
        >
          Retry
        </button>
      </div>

      <template v-else>
        <p
          v-if="numericJobId == null"
          class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-900 font-semibold text-sm"
        >
          Invalid job link.
        </p>

        <div
          v-else-if="!filteredApplications.length"
          class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-14 text-center"
        >
          <p class="text-slate-600 font-semibold">No applications yet.</p>
        </div>

        <ul v-else class="space-y-4">
          <li v-for="app in filteredApplications" :key="app.id">
            <ApplicationCard :application="app" />
          </li>
        </ul>
      </template>
    </main>
  </div>
</template>
