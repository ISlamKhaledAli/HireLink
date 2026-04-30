import { defineStore } from 'pinia'
import { loginApi, getUserApi, logoutApi, registerApi } from '@/services/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    roles: []
  }),

  actions: {
    async login(data) {
      const res = await loginApi(data)

      this.token = res.data.access_token
      localStorage.setItem('token', this.token)

      const user = await getUserApi()
      this.user = user.data
      this.roles = user.data.role_names || []
    },

    async register(data) {
      const res = await registerApi(data)

      this.token = res.data.access_token
      localStorage.setItem('token', this.token)

      const user = await getUserApi()
      this.user = user.data
      this.roles = user.data.role_names || []
    },

    async logout() {
      await logoutApi()

      this.user = null
      this.token = null
      this.roles = []

      localStorage.removeItem('token')
    }
    ,
    async fetchUser() {
  try {
    const res = await getUserApi()
    this.user = res.data
    this.roles = res.data.role_names || []
  } catch (e) {
    this.user = null
    this.token = null
    localStorage.removeItem('token')
  }
}
  }
})