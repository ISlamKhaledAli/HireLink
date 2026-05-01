import api from '@/services/api'

export const listApplications = () => api.get('/applications')

export const updateApplicationStatus = (id, status) =>
  api.put(`/applications/${id}`, { status })
