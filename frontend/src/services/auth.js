import api from './api'

export const loginApi = (data) => api.post('/login', data)
export const registerApi = (data) => api.post('/register', data)
export const getUserApi = () => api.get('/user')
export const logoutApi = () => api.post('/logout')
