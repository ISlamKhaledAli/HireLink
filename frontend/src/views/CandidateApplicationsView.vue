<script setup>
import { onMounted, computed } from 'vue'
import { useCandidateStore } from '@/stores/candidate'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { 
  Briefcase, 
  Search, 
  MoreVertical,
  XCircle,
  Loader2
} from 'lucide-vue-next'

const candidateStore = useCandidateStore()

onMounted(() => {
  candidateStore.fetchMyApplications()
})

const applications = computed(() => candidateStore.applications)
const isLoading = computed(() => candidateStore.loading)

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date)
}

async function handleWithdraw(id) {
  if (confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
    await candidateStore.cancelApplication(id)
  }
}
</script>

<template>
  <DashboardLayout>
    <!-- Page Specific Actions (Moved from old header) -->
    <div class="mb-6 flex justify-end">
      <div class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input 
          type="text" 
          placeholder="Search applications..." 
          class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-secondary focus:border-secondary focus:outline-none w-64 text-sm bg-white font-semibold shadow-sm"
        />
      </div>
    </div>

    <!-- Recent Applications Table Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div>
          <h3 class="text-lg font-black text-primary tracking-tight">Application History</h3>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Track your job seeking progress</p>
        </div>
        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-black">{{ applications.length }} Total</span>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
        <Loader2 class="w-10 h-10 text-primary animate-spin mb-4" />
        <p class="text-sm font-bold text-slate-500">Loading your applications...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="applications.length === 0" class="flex flex-col items-center justify-center py-20 text-center px-6">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 border border-slate-100">
          <Briefcase class="w-10 h-10 text-slate-300" />
        </div>
        <h3 class="text-xl font-black text-primary mb-2">No applications yet</h3>
        <p class="text-slate-500 font-bold text-sm mb-6 max-w-md">You haven't applied to any jobs yet. Start browsing opportunities to take the next step in your career.</p>
        <router-link to="/jobs" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity">
          Find Jobs
        </router-link>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-white text-slate-400 text-xs uppercase tracking-widest font-black border-b border-slate-100">
            <tr>
              <th class="px-6 py-4">Job Title</th>
              <th class="px-6 py-4">Company</th>
              <th class="px-6 py-4">Date Applied</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 text-sm font-semibold">
            <tr v-for="app in applications" :key="app.id" class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <div class="text-primary font-bold">{{ app.job?.title || 'Unknown Job' }}</div>
                <div class="text-xs text-slate-400 mt-1 capitalize">{{ app.job?.work_type || 'N/A' }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center font-black text-slate-500 text-xs border border-slate-200">
                    {{ (app.job?.user?.name || 'C')[0].toUpperCase() }}
                  </div>
                  <span class="text-slate-700">{{ app.job?.user?.name || 'Unknown Company' }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-500">
                {{ formatDate(app.created_at) }}
              </td>
              <td class="px-6 py-4">
                <span v-if="app.status === 'pending'" class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 text-[11px] font-black uppercase tracking-widest">
                  Pending
                </span>
                <span v-else-if="app.status === 'accepted'" class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black uppercase tracking-widest">
                  Accepted
                </span>
                <span v-else-if="app.status === 'rejected'" class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-[11px] font-black uppercase tracking-widest">
                  Rejected
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button 
                  v-if="app.status === 'pending'" 
                  @click="handleWithdraw(app.id)"
                  class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-red-600 transition-colors px-3 py-1.5 rounded-lg hover:bg-red-50"
                  title="Withdraw Application"
                >
                  <XCircle class="w-4 h-4" />
                  Withdraw
                </button>
                <button v-else class="text-slate-300 cursor-not-allowed" disabled>
                  <MoreVertical class="w-5 h-5 inline-block" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>