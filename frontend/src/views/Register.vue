<template>
  <div class="container">
    <h2>Register</h2>

    <input v-model="name" placeholder="Name" />
    <input v-model="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />
    <input v-model="password_confirmation" type="password" placeholder="Confirm Password" />

    <!-- 👇 اختيار role -->
    <select v-model="role">
      <option value="">Select Role</option>
      <option value="candidate">Candidate</option>
<option value="employer">Employer</option>
    </select>

    <button @click="register">Register</button>

    <p v-if="error" class="error">{{ error }}</p>

    <p>
      Already have an account?
      <router-link to="/login">Login</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const role = ref('')
const error = ref('')

const auth = useAuthStore()
const router = useRouter()

const register = async () => {
  error.value = ''

  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
      role: role.value 
    })

    if (auth.token) {
      router.push('/login')
    }
  } catch (err) {
    console.log(err.response?.data)
    error.value = err.response?.data?.message || 'Register failed'
  }
}
</script>

<style>
.container {
  width: 320px;
  margin: 100px auto;
  text-align: center;
}
input, select {
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