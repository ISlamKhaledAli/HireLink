import api from "./api";

export const getDashboardStatsApi = () => api.get("/admin/dashboard");

export const getPendingJobsApi = (page = 1) => api.get('/jobs', { params: { status: 'pending', page } })

export const approveJobApi = (id) => api.post(`/admin/jobs/${id}/approve`)

export const rejectJobApi = (id) => api.post(`/admin/jobs/${id}/reject`)