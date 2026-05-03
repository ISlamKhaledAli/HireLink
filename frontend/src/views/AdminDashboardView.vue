<script setup>
import { onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { useRouter } from 'vue-router'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { 
  Briefcase, 
  Users, 
  CheckCircle,
  XCircle,
  Clock,
  ArrowRight,
  Loader2
} from 'lucide-vue-next'

const adminStore = useAdminStore()
const router = useRouter()

onMounted(() => {
  adminStore.fetchDashboardStats()
})
</script>

<template>
  <DashboardLayout>
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
    <div v-if="!adminStore.loading" class="mt-8 bg-primary text-white p-8 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden flex flex-col justify-between md:flex-row md:items-center">
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
  </DashboardLayout>
</template>