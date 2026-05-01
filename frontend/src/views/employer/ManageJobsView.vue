<script setup>
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { Plus, Loader2, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { useJobStore } from '@/stores/jobStore'
import { getCategories } from '@/services/categories'
import JobForm from '@/components/employer/JobForm.vue'
import JobListingActions from '@/components/employer/JobListingActions.vue'

const jobStore = useJobStore()
const {
  myJobs,
  myJobsLoading,
  myJobsError,
  myJobsPagination,
  updatingJobId,
  deletingJobId
} = storeToRefs(jobStore)

const categories = ref([])
const categoriesLoading = ref(false)
const categoriesError = ref(null)

const listPage = ref(1)

const editingJob = ref(null)
const editForm = reactive({
  category_id: '',
  title: '',
  description: '',
  location: '',
  work_type: 'remote',
  salary_range: '',
  deadline: ''
})
const editSubmitError = ref('')

const minDeadline = computed(() => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  d.setDate(d.getDate() + 1)
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
})

const editErrors = computed(() => jobStore.editFieldErrors || {})

const editModalOpen = computed(() => editingJob.value != null)

function statusBadgeClass(status) {
  const map = {
    pending: 'border-amber-200 bg-amber-50 text-amber-900',
    approved: 'border-emerald-200 bg-emerald-50 text-emerald-900',
    rejected: 'border-red-200 bg-red-50 text-red-900'
  }
  return map[status] || 'border-slate-200 bg-slate-50 text-slate-800'
}

async function loadCategories() {
  categoriesLoading.value = true
  categoriesError.value = null
  try {
    const { data } = await getCategories()
    categories.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error(e)
    categoriesError.value = 'Could not load categories.'
  } finally {
    categoriesLoading.value = false
  }
}

async function loadJobs(page = listPage.value) {
  listPage.value = page
  await jobStore.fetchMyJobs({ page })
}

function rowBusy(job) {
  return updatingJobId.value === job.id || deletingJobId.value === job.id
}

function openEdit(job) {
  editingJob.value = job
  editSubmitError.value = ''
  jobStore.clearEditFieldErrors()
  Object.assign(editForm, {
    category_id: job.category_id,
    title: job.title || '',
    description: job.description || '',
    location: job.location || '',
    work_type: job.work_type || 'remote',
    salary_range: job.salary_range ? String(job.salary_range) : '',
    deadline: job.deadline || ''
  })
}

function closeEdit() {
  if (updatingJobId.value) return
  editingJob.value = null
  editSubmitError.value = ''
  jobStore.clearEditFieldErrors()
}

function buildEditPayload() {
  const payload = {
    category_id: Number(editForm.category_id),
    title: editForm.title.trim(),
    description: editForm.description.trim(),
    location: editForm.location.trim(),
    work_type: editForm.work_type,
    deadline: editForm.deadline
  }
  const sr = editForm.salary_range?.trim()
  if (sr) payload.salary_range = sr
  else payload.salary_range = null
  return payload
}

async function onEditSubmit() {
  if (!editingJob.value) return
  editSubmitError.value = ''
  jobStore.clearEditFieldErrors()
  try {
    await jobStore.updateJob(editingJob.value.id, buildEditPayload())
    closeEdit()
    await loadJobs()
  } catch (err) {
    if (err.response?.status === 422) return
    editSubmitError.value =
      err.response?.data?.message || err.message || 'Update failed. Try again.'
  }
}

async function onDelete(job) {
  if (
    !window.confirm(`Delete “${job.title}”? This cannot be undone.`)
  ) {
    return
  }
  try {
    await jobStore.deleteJob(job.id)
    await loadJobs()
    if (editingJob.value?.id === job.id) closeEdit()
  } catch (err) {
    console.error(err)
    window.alert(err.response?.data?.message || err.message || 'Could not delete this job.')
  }
}

const paginationMeta = computed(() => myJobsPagination.value || {})
const totalPages = computed(() => paginationMeta.value.last_page || 1)

watch(totalPages, (tp) => {
  if (listPage.value > tp && tp >= 1) loadJobs(tp)
})

onMounted(async () => {
  await loadCategories()
  await loadJobs(1)
})
</script>

<template>
  <div class="bg-[#FAFAFC] min-h-screen font-body text-slate-900">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:py-10">
      <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">My jobs</h1>
          <p class="mt-2 text-slate-500 font-semibold text-sm">
            Manage listings, edit details, or remove postings. Updates send listings back to pending
            review.
          </p>
        </div>
        <router-link
          :to="{ name: 'employer-jobs-new' }"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 font-black text-sm text-white shadow-lg shadow-primary/20 hover:opacity-95 transition-opacity"
        >
          <Plus class="h-4 w-4" aria-hidden="true" />
          Post a job
        </router-link>
      </header>

      <div
        v-if="categoriesError"
        class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-900"
      >
        {{ categoriesError }} Editing may be unavailable until categories load.
      </div>

      <div
        v-if="myJobsLoading && !myJobs.length"
        class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-6 text-slate-600 font-bold text-sm"
      >
        <Loader2 class="h-5 w-5 animate-spin text-primary" aria-hidden="true" />
        Loading your jobs…
      </div>

      <div
        v-else-if="myJobsError"
        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-semibold text-sm"
      >
        {{ myJobsError }}
        <button
          type="button"
          class="mt-3 font-black text-primary underline"
          @click="loadJobs()"
        >
          Retry
        </button>
      </div>

      <template v-else>
        <div
          v-if="!myJobs.length"
          class="rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-14 text-center"
        >
          <p class="text-slate-600 font-semibold">You have no job listings yet.</p>
          <router-link
            :to="{ name: 'employer-jobs-new' }"
            class="mt-4 inline-flex items-center gap-2 font-black text-primary text-sm hover:underline"
          >
            <Plus class="h-4 w-4" />
            Post your first job
          </router-link>
        </div>

        <div v-else class="space-y-4">
          <!-- Desktop table -->
          <div class="hidden md:block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
              <thead class="border-b border-slate-100 bg-slate-50/80">
                <tr>
                  <th class="px-4 py-3 font-black text-[11px] uppercase tracking-wider text-slate-400">
                    Title
                  </th>
                  <th class="px-4 py-3 font-black text-[11px] uppercase tracking-wider text-slate-400">
                    Category
                  </th>
                  <th class="px-4 py-3 font-black text-[11px] uppercase tracking-wider text-slate-400">
                    Status
                  </th>
                  <th class="px-4 py-3 font-black text-[11px] uppercase tracking-wider text-slate-400">
                    Deadline
                  </th>
                  <th class="px-4 py-3 font-black text-[11px] uppercase tracking-wider text-slate-400 text-right">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="job in myJobs" :key="job.id" class="hover:bg-slate-50/50">
                  <td class="px-4 py-3 font-bold text-slate-900">{{ job.title }}</td>
                  <td class="px-4 py-3 font-semibold text-slate-600">{{ job.category || '—' }}</td>
                  <td class="px-4 py-3">
                    <span
                      class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-black capitalize"
                      :class="statusBadgeClass(job.status)"
                    >
                      {{ job.status }}
                    </span>
                  </td>
                  <td class="px-4 py-3 font-semibold text-slate-600">{{ job.deadline }}</td>
                  <td class="px-4 py-3 text-right">
                    <JobListingActions
                      :job-id="job.id"
                      :disabled="rowBusy(job)"
                      @edit="openEdit(job)"
                      @delete="onDelete(job)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile cards -->
          <ul class="md:hidden space-y-3">
            <li
              v-for="job in myJobs"
              :key="job.id"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
            >
              <div class="flex items-start justify-between gap-2">
                <div>
                  <p class="font-black text-slate-900">{{ job.title }}</p>
                  <p class="mt-1 text-xs font-semibold text-slate-500">{{ job.category }}</p>
                </div>
                <span
                  class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] font-black capitalize"
                  :class="statusBadgeClass(job.status)"
                >
                  {{ job.status }}
                </span>
              </div>
              <p class="mt-2 text-xs font-bold text-slate-500">Deadline: {{ job.deadline }}</p>
              <div class="mt-4">
                <JobListingActions
                  :job-id="job.id"
                  :disabled="rowBusy(job)"
                  @edit="openEdit(job)"
                  @delete="onDelete(job)"
                />
              </div>
            </li>
          </ul>

          <div
            v-if="totalPages > 1"
            class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600"
          >
            <span>
              Page {{ paginationMeta.current_page }} of {{ totalPages }}
            </span>
            <div class="flex items-center gap-2">
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:border-primary disabled:opacity-40"
                :disabled="myJobsLoading || (paginationMeta.current_page || 1) <= 1"
                @click="loadJobs((paginationMeta.current_page || 1) - 1)"
              >
                <ChevronLeft class="h-4 w-4" />
                Prev
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:border-primary disabled:opacity-40"
                :disabled="myJobsLoading || (paginationMeta.current_page || 1) >= totalPages"
                @click="loadJobs((paginationMeta.current_page || 1) + 1)"
              >
                Next
                <ChevronRight class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </template>
    </main>

    <!-- Edit modal -->
    <Teleport to="body">
      <div
        v-if="editModalOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="edit-job-title"
      >
        <button
          type="button"
          class="absolute inset-0 bg-slate-900/50"
          aria-label="Close"
          @click="closeEdit"
        />
        <div
          class="relative z-[101] max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8"
          @click.stop
        >
          <h2 id="edit-job-title" class="text-xl font-black text-primary tracking-tight">
            Edit job
          </h2>
          <p class="mt-1 text-sm font-semibold text-slate-500">
            Saving will reset status to pending until an admin approves again.
          </p>

          <div
            v-if="editSubmitError"
            class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800"
          >
            {{ editSubmitError }}
          </div>

          <div class="mt-6">
            <JobForm
              v-model:form="editForm"
              :categories="categories"
              :errors="editErrors"
              :disabled="!!updatingJobId"
              :categories-loading="categoriesLoading"
              :min-deadline="minDeadline"
              submit-label="Save changes"
              use-emit-cancel
              @submit="onEditSubmit"
              @cancel="closeEdit"
            />
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
