<template>
  <div>
    <Navbar />

    <div class="container">
      <h1>Dashboard</h1>

      <p v-if="auth.user">
        Welcome {{ auth.user.name }}
      </p>

      <p v-if="isAdmin">🔥 You are Admin</p>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../components/Navbar.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const isAdmin = auth.roles.includes('admin')
</script>

<style>
.container {
  padding: 20px;
}
</style>