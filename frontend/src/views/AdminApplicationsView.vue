<script setup>
import { computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { Loader2 } from 'lucide-vue-next'
import { useApplicationsStore } from '@/stores/applications'
import ApplicationCard from '@/components/employer/ApplicationCard.vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const applicationsStore = useApplicationsStore()
const { items, loading, error } = storeToRefs(applicationsStore)

onMounted(() => {
  applicationsStore.fetchAll()
})
</script>

<template>
  <DashboardLayout>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
      <header class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">
          Platform Applications
        </h1>
        <p class="mt-2 text-slate-600 font-semibold text-sm">
          Monitor all applications submitted across the platform.
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
          @click="applicationsStore.fetchAll()"
        >
          Retry
        </button>
      </div>

      <template v-else>
        <div
          v-if="!items.length"
          class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-14 text-center"
        >
          <p class="text-slate-600 font-semibold">No applications have been submitted yet.</p>
        </div>

        <ul v-else class="space-y-4">
          <li v-for="app in items" :key="app.id">
            <!-- Reuse ApplicationCard, it works well. Note: Admins can't accept/reject since it's the employer's job, but they can view it. -->
            <ApplicationCard :application="app" />
          </li>
        </ul>
      </template>
    </div>
  </DashboardLayout>
</template>
