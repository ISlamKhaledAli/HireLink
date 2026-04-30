<template>
  <div class="container">
    <h2>Login</h2>

    <input v-model="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />

    <button @click="login">Login</button>

    <p v-if="error" class="error">{{ error }}</p>

    <p>
      Don't have an account?
      <router-link to="/register">Register</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')

const auth = useAuthStore()
const router = useRouter()

const login = async () => {
  error.value = ''

  try {
    await auth.login({
      email: email.value,
      password: password.value
    })

    if (auth.token) {
      router.push('/')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
    console.log(err.response?.data)
  }
}
</script>

<style>
.container {
  width: 300px;
  margin: 100px auto;
  text-align: center;
}
input {
  display: block;
  width: 100%;
  margin: 10px 0;
  padding: 8px;
}
button {
  width: 100%;
  padding: 8px;
}
.error {
  color: red;
}
</style>