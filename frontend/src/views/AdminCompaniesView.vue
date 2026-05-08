<script setup>
import { onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { 
  CheckCircle,
  Building2,
  Mail,
  Loader2,
  ChevronLeft,
  ChevronRight
} from 'lucide-vue-next'

const adminStore = useAdminStore()

onMounted(() => {
  adminStore.fetchPendingCompanies(1)
})

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  }).format(date)
}

async function handleApprove(id) {
  if (confirm('Are you sure you want to approve this company? They will be able to post jobs immediately.')) {
    try {
      await adminStore.approveCompany(id)
    } catch (error) {
      alert('Failed to approve company.')
    }
  }
}

function changePage(pageNumber) {
  if (pageNumber > 0 && pageNumber <= (adminStore.paginationCompanies.last_page || 1)) {
    adminStore.fetchPendingCompanies(pageNumber)
  }
}
</script>

<template>
  <DashboardLayout>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <div>
          <h2 class="text-lg font-black text-primary">Pending Companies</h2>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Employers awaiting platform verification</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="adminStore.loadingJobs" class="flex flex-col items-center justify-center py-20">
        <Loader2 class="w-10 h-10 text-primary animate-spin mb-4" />
        <p class="text-sm font-bold text-slate-500">Loading companies...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="adminStore.pendingCompanies.length === 0" class="flex flex-col items-center justify-center py-20 text-center px-6">
        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 border border-emerald-100">
          <Building2 class="w-10 h-10 text-emerald-500" />
        </div>
        <h3 class="text-xl font-black text-primary mb-2">No Pending Approvals</h3>
        <p class="text-slate-500 font-bold text-sm max-w-md">There are currently no employers awaiting verification.</p>
      </div>

      <!-- Companies Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-white text-slate-400 text-xs uppercase tracking-widest font-black border-b border-slate-100">
              <th class="px-6 py-4">Company Name</th>
              <th class="px-6 py-4">Contact Email</th>
              <th class="px-6 py-4">Registered Date</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 text-sm">
            <tr v-for="company in adminStore.pendingCompanies" :key="company.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary font-black">
                    {{ (company.name || 'C')[0].toUpperCase() }}
                  </div>
                  <div class="font-bold text-primary text-base">{{ company.name }}</div>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-500 font-semibold">
                <div class="flex items-center gap-2">
                  <Mail class="w-4 h-4" />
                  {{ company.email }}
                </div>
              </td>
              <td class="px-6 py-4 text-slate-500 font-semibold">
                 {{ formatDate(company.created_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="handleApprove(company.id)" 
                  class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl font-black text-xs hover:bg-emerald-600 hover:text-white transition-all border border-emerald-100"
                >
                  <CheckCircle class="w-4 h-4" />
                  Verify Company
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="adminStore.paginationCompanies && adminStore.paginationCompanies.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
        <span class="text-xs font-bold text-slate-400">
          Showing Page {{ adminStore.paginationCompanies.current_page }} of {{ adminStore.paginationCompanies.last_page }}
        </span>
        <div class="flex items-center gap-2">
          <button 
            @click="changePage(adminStore.paginationCompanies.current_page - 1)" 
            :disabled="adminStore.paginationCompanies.current_page === 1"
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed bg-white"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>
          <button 
            @click="changePage(adminStore.paginationCompanies.current_page + 1)" 
            :disabled="adminStore.paginationCompanies.current_page === adminStore.paginationCompanies.last_page"
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed bg-white"
          >
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
