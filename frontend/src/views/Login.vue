<template>
  <div class="login-container">
    <!-- Decorative Elements -->
    <div class="background-decoration"></div>
    
    <div class="login-card">
      <!-- Logo/Brand -->
      <div class="branding">
        <div class="logo-circle">💼</div>
        <h2 class="brand-name">HireLink</h2>
      </div>

      <!-- Header -->
      <div class="header">
        <h1 class="title">Welcome Back</h1>
        <p class="subtitle">Sign in to your professional account</p>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>{{ error }}</span>
      </div>

      <!-- Form -->
      <form @submit.prevent="login" class="form">
        <!-- Email Field -->
        <div class="form-group">
          <label for="email" class="label">Email Address</label>
          <div class="input-wrapper">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <input
              id="email"
              v-model="email"
              type="email"
              class="input"
              placeholder="your.email@example.com"
              required
            />
          </div>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="password" class="label">Password</label>
          <div class="input-wrapper password-wrapper">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              class="input"
              placeholder="Enter your password"
              required
            />
            <button
              type="button"
              class="toggle-password"
              @click="showPassword = !showPassword"
              :title="showPassword ? 'Hide password' : 'Show password'"
              aria-label="Toggle password visibility"
            >
              <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <!-- Login Button -->
        <button type="submit" class="btn btn-primary">
          <span>Sign In</span>
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </form>

      <!-- Divider -->
      <div class="divider">or</div>

      <!-- Footer Link -->
      <div class="footer">
        <p class="footer-text">
          Don't have an account?
          <router-link to="/register" class="link">Create one now</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')
const showPassword = ref(false)

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

<style scoped>
* {
  box-sizing: border-box;
}

.login-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
  position: relative;
  overflow: hidden;
}

.background-decoration {
  position: absolute;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.login-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.05);
  padding: 56px 48px;
  width: 100%;
  max-width: 420px;
  animation: slideInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  z-index: 1;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.branding {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 32px;
  animation: fadeIn 0.6s ease-out 0.1s both;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.logo-circle {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.brand-name {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
  letter-spacing: -0.5px;
}

.header {
  text-align: center;
  margin-bottom: 36px;
  animation: fadeIn 0.6s ease-out 0.2s both;
}

.title {
  font-size: 32px;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 12px 0;
  letter-spacing: -0.5px;
}

.subtitle {
  font-size: 15px;
  color: #6b7280;
  margin: 0;
  font-weight: 400;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 12px;
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  border: 1px solid #fca5a5;
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 24px;
  color: #991b1b;
  font-size: 14px;
  font-weight: 500;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  stroke-width: 2;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: fadeIn 0.6s ease-out 0.3s both;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.label {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  display: flex;
  align-items: center;
  gap: 4px;
}

.input-wrapper {
  display: flex;
  align-items: center;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background-color: #f9fafb;
  padding: 0 14px;
  overflow: hidden;
}

.input-wrapper:hover {
  border-color: #d1d5db;
  background-color: white;
}

.input-wrapper:focus-within {
  border-color: #6366f1;
  background-color: white;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08), 0 0 0 1px rgba(99, 102, 241, 0.3);
}

.input-wrapper.password-wrapper {
  position: relative;
}

.input-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  color: #9ca3af;
  stroke-width: 2;
  margin-right: 4px;
}

.input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px 4px;
  font-size: 15px;
  color: #1f2937;
  outline: none;
  font-weight: 500;
}

.input::placeholder {
  color: #bdbfc4;
  font-weight: 400;
}

.toggle-password {
  background: none;
  border: none;
  padding: 4px 8px;
  cursor: pointer;
  color: #9ca3af;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  margin: -4px -8px -4px 0;
}

.toggle-password svg {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

.toggle-password:hover {
  color: #6366f1;
  background: rgba(99, 102, 241, 0.05);
  border-radius: 8px;
}

.btn {
  padding: 12px 20px;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-top: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  color: white;
  font-weight: 600;
  letter-spacing: 0.3px;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.btn-primary:active {
  transform: translateY(0);
}

.btn-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2.5;
}

.divider {
  text-align: center;
  margin: 24px 0;
  color: #d1d5db;
  font-size: 14px;
  font-weight: 500;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
  z-index: -1;
}

.footer {
  text-align: center;
  margin-top: 24px;
}

.footer-text {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.link:hover {
  color: #4f46e5;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 480px) {
  .login-card {
    padding: 40px 28px;
    border-radius: 14px;
  }

  .title {
    font-size: 28px;
  }

  .logo-circle {
    width: 44px;
    height: 44px;
    font-size: 24px;
  }

  .btn {
    font-size: 15px;
    padding: 11px 18px;
  }

  .form {
    gap: 18px;
  }
}
</style>