import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import { getMyJobs } from '@/services/jobs'

export const useJobStore = defineStore('jobs', () => {
  const jobs = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({})

  const myJobs = ref([])
  const myJobsLoading = ref(false)
  const myJobsError = ref(null)
  const myJobsPagination = ref({})

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
    fetchMyJobs
  }
})
