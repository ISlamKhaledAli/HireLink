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
const AdminApplicationsView = () => import("@/views/AdminApplicationsView.vue");
const CompaniesView = () => import("@/views/CompaniesView.vue");
const PricingView = () => import("@/views/PricingView.vue");

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
    path: "/jobs/:id",
    name: "job-detail",
    component: () => import("@/views/JobDetailView.vue"),
    meta: { title: "Job Details" },
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
  {
    path: "/admin/companies",
    name: "admin-companies",
    component: () => import("@/views/AdminCompaniesView.vue"),
    meta: { requiresAuth: true, role: "admin", title: "Manage Companies" },
  },
  {
    path: "/admin/applications",
    component: AdminApplicationsView,
    meta: { requiresAuth: true, role: "admin", title: "All Applications" },
  },
  {
    path: "/companies",
    name: "companies",
    component: CompaniesView,
    meta: { title: "Companies" },
  },
  {
    path: "/pricing",
    name: "pricing",
    component: PricingView,
    meta: { title: "Pricing" },
  },
  {
    path: '/employer/jobs',
    name: 'employer-jobs',
    component: () => import('@/views/employer/ManageJobsView.vue'),
    meta: { requiresAuth: true, requiresEmployer: true, title: 'My jobs' }
  },
  {
    path: '/employer/jobs/:jobId/applications',
    name: 'employer-job-applications',
    component: () => import('@/views/employer/JobApplicationsView.vue'),
    meta: { requiresAuth: true, requiresEmployer: true, title: 'Applications' }
  },
  {
    path: '/employer/jobs/new',
    name: 'employer-jobs-new',
    component: () => import('@/views/employer/PostJobView.vue'),
    meta: { requiresAuth: true, requiresEmployer: true, title: 'Post a job' }
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

  if (to.meta.requiresEmployer) {
    const names = auth.roles || []
    const isEmployer =
      names.includes('employer') ||
      (Array.isArray(auth.user?.roles) &&
        auth.user.roles.some((r) => (typeof r === 'string' ? r : r?.name) === 'employer'))
    if (!isEmployer) {
      return next('/')
    }
  }

  if (to.meta.role && !auth.roles.includes(to.meta.role)) {
    console.warn(`Access denied. Requires role: ${to.meta.role}`);
    return next("/");
  }

  next()
})

export default router