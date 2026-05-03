import { defineStore } from 'pinia'
import { ref } from 'vue'
import { listApplications, updateApplicationStatus } from '@/services/applications'

function appKey(id) {
  return String(id)
}

export const useApplicationsStore = defineStore('applications', () => {
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)
  const updatingIds = ref({})
  const statusErrorById = ref({})

  function isUpdating(applicationId) {
    return !!updatingIds.value[appKey(applicationId)]
  }

  function statusErrorFor(applicationId) {
    return statusErrorById.value[appKey(applicationId)] ?? ''
  }

  function clearStatusError(applicationId) {
    const k = appKey(applicationId)
    const { [k]: _, ...rest } = statusErrorById.value
    statusErrorById.value = rest
  }

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

  async function setStatus(applicationId, status) {
    if (status !== 'accepted' && status !== 'rejected') return
    const id = applicationId
    const key = appKey(id)
    if (updatingIds.value[key]) return

    clearStatusError(id)
    updatingIds.value = { ...updatingIds.value, [key]: true }

    const idx = items.value.findIndex((a) => appKey(a.id) === key)
    const prev = idx >= 0 ? items.value[idx] : null

    try {
      const { data } = await updateApplicationStatus(id, status)
      if (idx >= 0 && prev) {
        items.value = items.value.map((a, i) =>
          i === idx
            ? {
                ...prev,
                ...data,
                job: data.job ?? prev.job,
                candidate: data.candidate ?? prev.candidate
              }
            : a
        )
      }
    } catch (err) {
      const body = err.response?.data
      const msg =
        (typeof body?.message === 'string' && body.message) ||
        (Array.isArray(body?.errors?.status) && body.errors.status[0]) ||
        'Could not update status.'
      statusErrorById.value = { ...statusErrorById.value, [key]: msg }
    } finally {
      const next = { ...updatingIds.value }
      delete next[key]
      updatingIds.value = next
    }
  }

  return {
    items,
    loading,
    error,
    updatingIds,
    statusErrorById,
    isUpdating,
    statusErrorFor,
    fetchAll,
    setStatus
  }
})
