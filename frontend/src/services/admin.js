import api from "./api";

export const getDashboardStatsApi = () => api.get("/admin/dashboard");

export const getPendingJobsApi = (page = 1) => api.get('/admin/jobs/pending', { params: { page } })

export const approveJobApi = (id) => api.post(`/admin/jobs/${id}/approve`)

export const rejectJobApi = (id) => api.post(`/admin/jobs/${id}/reject`)

export const getPendingCompaniesApi = (page = 1) => api.get('/admin/companies/pending', { params: { page } })
export const approveCompanyApi = (id) => api.post(`/admin/companies/${id}/approve`)