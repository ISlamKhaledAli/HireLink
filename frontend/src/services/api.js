import axios from 'axios'

const rawBase =
  import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'
const baseURL = String(rawBase).replace(/\/+$/, '')

const api = axios.create({
  baseURL
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem("token");

      if (window.location.pathname !== "/login") {
        window.location.href = "/login";
      }
    }

    return Promise.reject(error);
  },
);
export default api