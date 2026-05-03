import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import Dashboard from '@/views/Dashboard.vue'
import { useAuthStore } from '@/stores/auth'

const CandidateApplicationsView = () =>
  import("@/views/CandidateApplicationsView.vue");
const ProfileView = () => import("@/views/ProfileView.vue");
const AdminDashboardView = () => import("@/views/AdminDashboardView.vue");
const AdminJobsView = () => import("@/views/AdminJobsView.vue");

const routes = [
  { path: "/login", component: Login, meta: { title: "Login" } },
  { path: "/register", component: Register, meta: { title: "Register" } },
  {
    path: "/jobs",
    name: "jobs",
    component: () => import("@/views/JobsView.vue"),
    meta: { title: "Job Listings" },
  },
  {
    path: "/",
    component: Dashboard,
    meta: { requiresAuth: true, title: "Dashboard" },
  },

  {
    path: "/candidate/applications",
    component: CandidateApplicationsView,
    meta: { requiresAuth: true, role: "candidate", title: "My Applications" },
  },
  {
    path: "/profile",
    component: ProfileView,
    meta: { requiresAuth: true, title: "My Profile" },
  },

  {
    path: "/admin",
    component: AdminDashboardView,
    meta: { requiresAuth: true, role: "admin", title: "Admin Dashboard" },
  },
  {
    path: "/admin/jobs",
    component: AdminJobsView,
    meta: { requiresAuth: true, role: "admin", title: "Manage Jobs" },
  },
];

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

  if (to.meta.role && !auth.roles.includes(to.meta.role)) {
    console.warn(`Access denied. Requires role: ${to.meta.role}`);
    return next("/");
  }

  next()
})

export default router