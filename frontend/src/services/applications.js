import api from "./api";

export const getMyApplicationsApi = () => api.get("/applications");
export const cancelApplicationApi = (id) => api.delete(`/applications/${id}`);

export const listApplications = () => api.get("/applications");

export const updateApplicationStatus = (id, status) =>
  api.put(`/applications/${id}`, { status });

export const applyForJob = (formData) =>
  api.post("/applications", formData, {
    headers: { "Content-Type": "multipart/form-data" },
  });
