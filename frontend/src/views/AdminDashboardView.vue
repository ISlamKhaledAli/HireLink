<script setup>
import { onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { 
  LayoutDashboard, 
  Briefcase, 
  Users, 
  Settings,
  Bell,
  Search,
  CheckCircle,
  XCircle,
  Clock,
  ArrowRight,
  Loader2
} from 'lucide-vue-next'

const adminStore = useAdminStore()
const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  adminStore.fetchDashboardStats()
})
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex">
    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col z-40">
      <div class="text-xl font-black text-primary px-6 py-8 tracking-tight">
        HireLink <span class="text-secondary text-sm">Admin</span>
      </div>
      <nav class="flex flex-col h-full space-y-1">
        <!-- Active State for Dashboard -->
        <router-link to="/admin" class="flex items-center gap-3 bg-blue-50 text-blue-900 border-r-4 border-blue-900 px-6 py-3 font-black text-sm">
          <LayoutDashboard class="w-5 h-5" />
          Overview
        </router-link>
        
        <router-link to="/admin/jobs" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <Briefcase class="w-5 h-5" />
          Manage Jobs
        </router-link>

        <a href="#" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <Users class="w-5 h-5" />
          Users
        </a>

        <a href="#" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <Settings class="w-5 h-5" />
          Settings
        </a>
      </nav>
      
      <div class="mt-auto p-6 border-t border-slate-100">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-black border border-blue-200">
            A
          </div>
          <div>
            <div class="font-bold text-primary text-sm">{{ authStore.user?.name || 'Administrator' }}</div>
            <div class="text-slate-500 text-xs font-semibold">System Admin</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <h1 class="text-2xl font-black text-primary tracking-tight">System Overview</h1>
        
        <div class="flex items-center gap-6">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input 
              type="text" 
              placeholder="Search data..." 
              class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-secondary focus:border-secondary outline-none w-64 transition-all"
            />
          </div>
          <button class="relative text-slate-400 hover:text-primary transition-colors">
            <Bell class="w-6 h-6" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-secondary rounded-full border-2 border-white"></span>
          </button>
        </div>
      </header>

      <div class="p-8 max-w-7xl mx-auto w-full space-y-8">
        
        <!-- Loading State -->
        <div v-if="adminStore.loading" class="flex flex-col items-center justify-center py-20">
          <Loader2 class="w-10 h-10 text-primary animate-spin mb-4" />
          <p class="text-sm font-bold text-slate-500">Compiling platform statistics...</p>
        </div>

        <!-- Stats Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
          
          <!-- Total Users -->
          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col hover:border-blue-200 transition-colors">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <Users class="w-6 h-6" />
              </div>
            </div>
            <span class="text-slate-500 font-bold text-sm mb-1">Total Users</span>
            <span class="font-black text-3xl text-primary">{{ adminStore.stats.total_users }}</span>
          </div>

          <!-- Total Jobs -->
          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col hover:border-slate-300 transition-colors">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <Briefcase class="w-6 h-6" />
              </div>
            </div>
            <span class="text-slate-500 font-bold text-sm mb-1">Total Jobs</span>
            <span class="font-black text-3xl text-primary">{{ adminStore.stats.total_jobs }}</span>
          </div>

          <!-- Pending Jobs -->
          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col hover:border-orange-200 transition-colors">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                <Clock class="w-6 h-6" />
              </div>
            </div>
            <span class="text-slate-500 font-bold text-sm mb-1">Pending Approval</span>
            <span class="font-black text-3xl text-primary">{{ adminStore.stats.pending_jobs }}</span>
          </div>

          <!-- Approved Jobs -->
          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col hover:border-emerald-200 transition-colors">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <CheckCircle class="w-6 h-6" />
              </div>
            </div>
            <span class="text-slate-500 font-bold text-sm mb-1">Approved Jobs</span>
            <span class="font-black text-3xl text-primary">{{ adminStore.stats.approved_jobs }}</span>
          </div>

          <!-- Rejected Jobs -->
          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col hover:border-red-200 transition-colors">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <XCircle class="w-6 h-6" />
              </div>
            </div>
            <span class="text-slate-500 font-bold text-sm mb-1">Rejected Jobs</span>
            <span class="font-black text-3xl text-primary">{{ adminStore.stats.rejected_jobs }}</span>
          </div>

        </div>

        <!-- Quick Action / Banner -->
        <div v-if="!adminStore.loading" class="bg-primary text-white p-8 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden flex flex-col justify-between md:flex-row md:items-center">
          <div class="relative z-10 space-y-2">
            <h3 class="text-2xl font-black">Needs Attention</h3>
            <p class="text-blue-200 text-sm font-semibold max-w-md">
              You have <span class="text-white font-black">{{ adminStore.stats.pending_jobs }}</span> job listings awaiting your approval. Review them to keep the platform active.
            </p>
          </div>
          <div class="relative z-10 mt-6 md:mt-0">
            <button @click="router.push('/admin/jobs')" class="bg-secondary text-white px-8 py-3 rounded-xl font-black text-sm hover:scale-105 active:scale-95 transition-all shadow-lg shadow-secondary/20 flex items-center gap-2">
              Review Pending Jobs <ArrowRight class="w-4 h-4" />
            </button>
          </div>
          
          <!-- Decorative Background -->
          <div class="absolute right-0 bottom-0 w-full h-full opacity-20 pointer-events-none">
            <div class="w-full h-full bg-[radial-gradient(circle_at_bottom_right,_var(--tw-gradient-stops))] from-secondary via-transparent to-transparent"></div>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>