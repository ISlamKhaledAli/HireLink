<template>
  <div class="nav">
    <span v-if="auth.user">
      👤 {{ auth.user.name }}
    </span>

    <router-link to="/">Dashboard</router-link>

    <router-link v-if="!auth.token" to="/login">Login</router-link>

    <button v-if="auth.token" @click="logout">Logout</button>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

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