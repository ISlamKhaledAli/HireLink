<script setup>
import { onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { useAuthStore } from '@/stores/auth'
import { 
  LayoutDashboard, 
  Briefcase, 
  Users, 
  Settings,
  Bell,
  Search,
  CheckCircle,
  XCircle,
  Eye,
  Loader2,
  ChevronLeft,
  ChevronRight
} from 'lucide-vue-next'

const adminStore = useAdminStore()
const authStore = useAuthStore()

onMounted(() => {
  adminStore.fetchPendingJobs(1)
})

// Formatting Date
function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  }).format(date)
}

// Handlers with Confirmation
async function handleApprove(id) {
  if (confirm('Are you sure you want to approve this job listing? It will be visible to all candidates immediately.')) {
    try {
      await adminStore.approveJob(id)
    } catch (error) {
      alert('Failed to approve job.')
    }
  }
}

async function handleReject(id) {
  if (confirm('Are you sure you want to reject this job listing? The employer will be notified.')) {
    try {
      await adminStore.rejectJob(id)
    } catch (error) {
      alert('Failed to reject job.')
    }
  }
}

// Pagination Logic
function changePage(pageNumber) {
  if (pageNumber > 0 && pageNumber <= (adminStore.pagination.last_page || 1)) {
    adminStore.fetchPendingJobs(pageNumber)
  }
}
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex">
    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col z-40">
      <div class="text-xl font-black text-primary px-6 py-8 tracking-tight">
        HireLink <span class="text-secondary text-sm">Admin</span>
      </div>
      <nav class="flex flex-col h-full space-y-1">
        <router-link to="/admin" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <LayoutDashboard class="w-5 h-5" />
          Overview
        </router-link>
        
        <!-- Active State for Manage Jobs -->
        <router-link to="/admin/jobs" class="flex items-center gap-3 bg-blue-50 text-blue-900 border-r-4 border-blue-900 px-6 py-3 font-black text-sm">
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

    <!-- Main Content -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <h1 class="text-2xl font-black text-primary tracking-tight">Pending Jobs Approval</h1>
        
        <div class="flex items-center gap-6">
          <button class="relative text-slate-400 hover:text-primary transition-colors">
            <Bell class="w-6 h-6" />
          </button>
        </div>
      </header>

      <div class="p-8 max-w-7xl mx-auto w-full space-y-8">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
              <h2 class="text-lg font-black text-primary">Awaiting Review</h2>
              <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Jobs that need your approval to go live</p>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="adminStore.loadingJobs" class="flex flex-col items-center justify-center py-20">
            <Loader2 class="w-10 h-10 text-primary animate-spin mb-4" />
            <p class="text-sm font-bold text-slate-500">Loading pending jobs...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="adminStore.pendingJobs.length === 0" class="flex flex-col items-center justify-center py-20 text-center px-6">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 border border-emerald-100">
              <CheckCircle class="w-10 h-10 text-emerald-500" />
            </div>
            <h3 class="text-xl font-black text-primary mb-2">All Caught Up!</h3>
            <p class="text-slate-500 font-bold text-sm max-w-md">There are currently no pending jobs awaiting your approval. You've cleared the queue.</p>
          </div>

          <!-- Jobs Table -->
          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-white text-slate-400 text-xs uppercase tracking-widest font-black border-b border-slate-100">
                  <th class="px-6 py-4">Job Details</th>
                  <th class="px-6 py-4">Employer</th>
                  <th class="px-6 py-4">Submitted Date</th>
                  <th class="px-6 py-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 text-sm">
                <tr v-for="job in adminStore.pendingJobs" :key="job.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-bold text-primary text-base">{{ job.title }}</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1 capitalize">
                      {{ job.work_type }} • {{ job.location }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-xs font-black text-primary">
                        {{ (job.user?.name || 'E')[0].toUpperCase() }}
                      </div>
                      <span class="font-bold text-slate-700">{{ job.user?.name || 'Unknown Company' }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-slate-500 font-semibold">
                    {{ formatDate(job.created_at) }}
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <router-link :to="`/jobs/${job.id}`" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Preview Job">
                        <Eye class="w-5 h-5" />
                      </router-link>
                      <button @click="handleApprove(job.id)" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Approve">
                        <CheckCircle class="w-5 h-5" />
                      </button>
                      <button @click="handleReject(job.id)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                        <XCircle class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div v-if="adminStore.pagination && adminStore.pagination.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <span class="text-xs font-bold text-slate-400">
              Showing Page {{ adminStore.pagination.current_page }} of {{ adminStore.pagination.last_page }}
            </span>
            <div class="flex items-center gap-2">
              <button 
                @click="changePage(adminStore.pagination.current_page - 1)" 
                :disabled="adminStore.pagination.current_page === 1"
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed bg-white"
              >
                <ChevronLeft class="w-4 h-4" />
              </button>
              <button 
                @click="changePage(adminStore.pagination.current_page + 1)" 
                :disabled="adminStore.pagination.current_page === adminStore.pagination.last_page"
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed bg-white"
              >
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>

        </div>

      </div>
    </main>
  </div>
</template>