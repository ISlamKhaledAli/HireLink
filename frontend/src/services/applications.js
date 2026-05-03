import api from "./api";

export const getMyApplicationsApi = () => api.get("/applications");
export const cancelApplicationApi = (id) => api.delete(`/applications/${id}`);
