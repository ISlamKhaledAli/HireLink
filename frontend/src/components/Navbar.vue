<template>
  <div class="nav">
    <span v-if="auth.user">
      👤 {{ auth.user.name }}
    </span>

    <router-link to="/">Dashboard</router-link>

    <router-link to="/jobs">Jobs</router-link>

    <template v-if="isEmployer">
      <router-link :to="{ name: 'employer-jobs-new' }">Post a job</router-link>
      <router-link :to="{ name: 'employer-jobs' }">My jobs</router-link>
    </template>

    <router-link v-if="!auth.token" to="/login">Login</router-link>

    <button v-if="auth.token" @click="logout">Logout</button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const isEmployer = computed(() => {
  const names = auth.roles || []
  if (names.includes('employer')) return true
  const roles = auth.user?.roles
  if (!Array.isArray(roles)) return false
  return roles.some((r) => (typeof r === 'string' ? r : r?.name) === 'employer')
})

const logout = () => {
  auth.logout()
}
</script>

<style>
.nav {
  background: #333;
  color: white;
  padding: 10px;
}
.nav a {
  color: white;
  margin: 0 10px;
}
button {
  margin-left: 10px;
}
</style>