<script setup>
import { reactive, computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useJobStore } from '@/stores/jobStore'
import { getCategories } from '@/services/categories'
import JobForm from '@/components/employer/JobForm.vue'
import { ArrowLeft, Loader2 } from 'lucide-vue-next'

const router = useRouter()
const jobStore = useJobStore()

const categories = ref([])
const categoriesLoading = ref(false)
const categoriesError = ref(null)
const submitError = ref('')

const form = reactive({
  category_id: '',
  title: '',
  description: '',
  location: '',
  work_type: 'remote',
  salary_range: '',
  deadline: ''
})

const minDeadline = computed(() => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  d.setDate(d.getDate() + 1)
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
})

const displayErrors = computed(() => jobStore.createFieldErrors || {})

onMounted(async () => {
  categoriesLoading.value = true
  categoriesError.value = null
  try {
    const { data } = await getCategories()
    categories.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error(e)
    categoriesError.value = 'Could not load categories. Refresh and try again.'
  } finally {
    categoriesLoading.value = false
  }
})

function buildPayload() {
  const payload = {
    category_id: Number(form.category_id),
    title: form.title.trim(),
    description: form.description.trim(),
    location: form.location.trim(),
    work_type: form.work_type,
    deadline: form.deadline
  }
  const sr = form.salary_range?.trim()
  if (sr) payload.salary_range = sr
  return payload
}

async function onSubmit() {
  submitError.value = ''
  jobStore.clearCreateFieldErrors()
  try {
    await jobStore.createJob(buildPayload())
    router.push({ name: 'employer-jobs' })
  } catch (err) {
    if (err.response?.status === 422) return
    submitError.value =
      err.response?.data?.message || err.message || 'Something went wrong. Please try again.'
  }
}
</script>

<template>
  <div class="bg-[#FAFAFC] min-h-screen font-body text-slate-900">
    <main class="mx-auto max-w-2xl px-4 py-8 sm:py-10">
      <router-link
        :to="{ name: 'employer-jobs' }"
        class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary transition-colors mb-6"
      >
        <ArrowLeft class="w-4 h-4" />
        Back to my jobs
      </router-link>

      <header class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">Post a job</h1>
        <p class="mt-2 text-slate-500 font-semibold text-sm">
          Your listing will be submitted for review before it appears publicly.
        </p>
      </header>

      <div
        v-if="categoriesLoading"
        class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-slate-600 font-bold text-sm"
      >
        <Loader2 class="w-5 h-5 animate-spin text-primary" />
        Loading categories…
      </div>

      <div
        v-else-if="categoriesError"
        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-semibold text-sm"
      >
        {{ categoriesError }}
      </div>

      <div
        v-else
        class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm"
      >
        <p
          v-if="submitError"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800"
        >
          {{ submitError }}
        </p>

        <JobForm
          v-model:form="form"
          :categories="categories"
          :errors="displayErrors"
          :disabled="jobStore.creating"
          :categories-loading="categoriesLoading"
          :min-deadline="minDeadline"
          @submit="onSubmit"
        />
      </div>
    </main>
  </div>
</template>
