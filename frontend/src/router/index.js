import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import Dashboard from '@/views/Dashboard.vue'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/login', component: Login, meta: { title: 'Login' } },
  { path: '/register', component: Register, meta: { title: 'Register' } },
  { path: '/jobs', name: 'jobs', component: () => import('@/views/JobsView.vue'), meta: { title: 'Job Listings' } },
  { path: '/', component: Dashboard, meta: { requiresAuth: true, title: 'Dashboard' } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} | HireLink` : 'HireLink'
  const auth = useAuthStore()
  if (auth.token && !auth.user) {
    await auth.fetchUser()
  }

  if (to.meta.requiresAuth && !auth.token) {
    return next('/login')
  }

  next()
})

export default router