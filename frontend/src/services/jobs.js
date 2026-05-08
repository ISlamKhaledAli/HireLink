import api from '@/services/api'

export const getMyJobs = (params = {}) => api.get('/employer/jobs', { params })

export const createJob = (payload) => api.post('/jobs', payload)

export const updateJob = (id, payload) => api.put(`/jobs/${id}`, payload)

export const deleteJob = (id) => api.delete(`/jobs/${id}`)

export const getJob = (id) => api.get(`/jobs/${id}`)

export const getJobComments = (jobId) => api.get(`/jobs/${jobId}/comments`)

export const postComment = (data) => api.post('/comments', data)

export const deleteComment = (id) => api.delete(`/comments/${id}`)
