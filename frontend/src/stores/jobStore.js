import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import {
  getMyJobs,
  createJob as postJobRequest,
  updateJob as putJobRequest,
  deleteJob as deleteJobRequest
} from '@/services/jobs'

function mapValidationErrors(responseData) {
  const bag = responseData?.errors
  if (!bag || typeof bag !== 'object') return {}
  const out = {}
  for (const [key, val] of Object.entries(bag)) {
    out[key] = Array.isArray(val) ? val[0] : String(val)
  }
  return out
}

export const useJobStore = defineStore('jobs', () => {
  const jobs = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({})

  const myJobs = ref([])
  const myJobsLoading = ref(false)
  const myJobsError = ref(null)
  const myJobsPagination = ref({})

  const creating = ref(false)
  const createFieldErrors = ref({})

  const updatingJobId = ref(null)
  const deletingJobId = ref(null)
  const editFieldErrors = ref({})

  async function fetchJobs(filters = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/jobs', { params: filters })
      jobs.value = response.data.data
      pagination.value = response.data.meta
    } catch (err) {
      console.error('Fetch Jobs Error:', err)
      error.value = 'Failed to load jobs.'
    } finally {
      loading.value = false
    }
  }

  async function fetchMyJobs(filters = {}) {
    myJobsLoading.value = true
    myJobsError.value = null
    try {
      const response = await getMyJobs(filters)
      myJobs.value = response.data.data
      myJobsPagination.value = response.data.meta
    } catch (err) {
      console.error('Fetch My Jobs Error:', err)
      myJobsError.value = 'Failed to load your jobs.'
    } finally {
      myJobsLoading.value = false
    }
  }

  function clearCreateFieldErrors() {
    createFieldErrors.value = {}
  }

  function clearEditFieldErrors() {
    editFieldErrors.value = {}
  }

  /**
   * @returns {Promise<object>} created job resource from API `data`
   */
  async function createJob(payload) {
    creating.value = true
    createFieldErrors.value = {}
    try {
      const response = await postJobRequest(payload)
      const created = response.data?.data
      try {
        await fetchMyJobs()
      } catch (e) {
        console.error('Refresh my jobs after create:', e)
      }
      return created
    } catch (err) {
      if (err.response?.status === 422) {
        createFieldErrors.value = mapValidationErrors(err.response.data)
      }
      throw err
    } finally {
      creating.value = false
    }
  }

  async function updateJob(id, payload) {
    updatingJobId.value = id
    editFieldErrors.value = {}
    try {
      await putJobRequest(id, payload)
    } catch (err) {
      if (err.response?.status === 422) {
        editFieldErrors.value = mapValidationErrors(err.response.data)
      }
      throw err
    } finally {
      updatingJobId.value = null
    }
  }

  async function deleteJob(id) {
    deletingJobId.value = id
    try {
      await deleteJobRequest(id)
    } finally {
      deletingJobId.value = null
    }
  }

  return {
    jobs,
    loading,
    error,
    pagination,
    fetchJobs,
    myJobs,
    myJobsLoading,
    myJobsError,
    myJobsPagination,
    fetchMyJobs,
    creating,
    createFieldErrors,
    clearCreateFieldErrors,
    createJob,
    updatingJobId,
    deletingJobId,
    editFieldErrors,
    clearEditFieldErrors,
    updateJob,
    deleteJob
  }
})
