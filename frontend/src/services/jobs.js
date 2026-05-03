import api from '@/services/api'

export const getMyJobs = (params = {}) => api.get('/employer/jobs', { params })

export const createJob = (payload) => api.post('/jobs', payload)

export const updateJob = (id, payload) => api.put(`/jobs/${id}`, payload)

export const deleteJob = (id) => api.delete(`/jobs/${id}`)
