import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useJobStore = defineStore('jobs', () => {
  const jobs = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({})

  async function fetchJobs(filters = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/jobs', { params: filters })
      // Assuming response structure: { data: [...], meta: {...} }
      // The API base URL is already /api, so we call /jobs
      jobs.value = response.data.data
      pagination.value = response.data.meta
    } catch (err) {
      console.error('Fetch Jobs Error:', err)
      error.value = 'Failed to load jobs.'
    } finally {
      loading.value = false
    }
  }

  return { jobs, loading, error, pagination, fetchJobs }
})
