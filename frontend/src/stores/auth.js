import { defineStore } from 'pinia'
import { loginApi, getUserApi, logoutApi, registerApi } from '@/services/auth'

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    roles: [],
  }),

  actions: {
    async login(data) {
      const res = await loginApi(data);

      this.token = res.data.access_token;
      localStorage.setItem("token", this.token);

      await this.fetchUser();
    },

    async register(data) {
      const res = await registerApi(data);

      this.token = res.data.access_token;
      localStorage.setItem("token", this.token);

      await this.fetchUser();
    },

    async logout() {
      await logoutApi();

      this.user = null;
      this.token = null;
      this.roles = [];

      localStorage.removeItem("token");
    },
    async fetchUser() {
      try {
        const res = await getUserApi();
        this.user = res.data.user || res.data;

        this.roles =
          res.data.role_names ||
          (this.user.roles ? this.user.roles.map((r) => r.name) : []);
      } catch (e) {
        console.error("Auth fetch user failed", e);
        this.user = null;
        this.token = null;
        this.roles = [];
        localStorage.removeItem("token");
      }
    },
  },
});