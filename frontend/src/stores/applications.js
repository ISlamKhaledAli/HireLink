import { defineStore } from 'pinia'
import { ref } from 'vue'
import { listApplications } from '@/services/applications'

export const useApplicationsStore = defineStore('applications', () => {
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchAll() {
    loading.value = true
    error.value = null
    try {
      const { data } = await listApplications()
      items.value = Array.isArray(data) ? data : []
    } catch (err) {
      console.error('Fetch applications:', err)
      error.value = 'Failed to load applications.'
      items.value = []
    } finally {
      loading.value = false
    }
  }

  return {
    items,
    loading,
    error,
    fetchAll
  }
})
