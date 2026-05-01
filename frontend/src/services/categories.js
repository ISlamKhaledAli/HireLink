import api from '@/services/api'

export const getCategories = () => api.get('/categories')
