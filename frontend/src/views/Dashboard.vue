<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { 
  LayoutDashboard, 
  Briefcase, 
  User as UserIcon, 
  Settings,
  ShieldCheck,
  Search,
  ArrowRight,
  LogOut
} from 'lucide-vue-next'

const authStore = useAuthStore()
const router = useRouter()

const isAdmin = computed(() => authStore.roles.includes('admin'))
const isCandidate = computed(() => authStore.roles.includes('candidate'))
// Optional: Add Employer later if needed
// const isEmployer = computed(() => authStore.roles.includes('employer'))

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex flex-col">
    <!-- Clean Minimal Header -->
    <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center font-black text-xl">H</div>
        <h1 class="text-xl font-black text-primary tracking-tight">HireLink Portal</h1>
      </div>
      
      <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-bold text-primary">{{ authStore.user?.name }}</p>
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">{{ authStore.roles.join(', ') || 'User' }}</p>
        </div>
        <button @click="handleLogout" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Logout">
          <LogOut class="w-5 h-5" />
        </button>
      </div>
    </header>

    <!-- Main Portal Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full p-8 flex flex-col items-center justify-center">
      
      <div class="text-center mb-12">
        <h2 class="text-4xl font-black text-primary mb-4 tracking-tight">Welcome back, {{ authStore.user?.name.split(' ')[0] }}!</h2>
        <p class="text-slate-500 font-bold max-w-xl mx-auto">Where would you like to go today? Select a portal below to access your workspace.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-4xl">
        
        <!-- === ADMIN PORTAL CARD === -->
        <div v-if="isAdmin" class="bg-white rounded-3xl p-8 border-2 border-transparent hover:border-primary shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer relative overflow-hidden" @click="router.push('/admin')">
          <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center mb-6">
            <ShieldCheck class="w-8 h-8" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Admin Workspace</h3>
          <p class="text-slate-500 font-semibold mb-8">Manage users, approve job listings, and view platform analytics.</p>
          
          <div class="flex items-center text-primary font-bold gap-2 group-hover:gap-4 transition-all">
            Enter Dashboard <ArrowRight class="w-5 h-5" />
          </div>
        </div>

        <!-- === CANDIDATE PORTAL CARD === -->
        <div v-if="isCandidate" class="bg-white rounded-3xl p-8 border-2 border-transparent hover:border-secondary shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer relative overflow-hidden" @click="router.push('/candidate/applications')">
          <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6">
            <Briefcase class="w-8 h-8" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">My Applications</h3>
          <p class="text-slate-500 font-semibold mb-8">Track the status of your job applications and withdraw pending requests.</p>
          
          <div class="flex items-center text-secondary font-bold gap-2 group-hover:gap-4 transition-all">
            View Applications <ArrowRight class="w-5 h-5" />
          </div>
        </div>

        <!-- === FIND JOBS CARD (Available to all) === -->
        <div class="bg-white rounded-3xl p-8 border-2 border-transparent hover:border-emerald-500 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer relative overflow-hidden" @click="router.push('/jobs')">
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
            <Search class="w-8 h-8" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Explore Jobs</h3>
          <p class="text-slate-500 font-semibold mb-8">Search for new opportunities and filter by category, location, or salary.</p>
          
          <div class="flex items-center text-emerald-600 font-bold gap-2 group-hover:gap-4 transition-all">
            Start Searching <ArrowRight class="w-5 h-5" />
          </div>
        </div>

        <!-- === PROFILE CARD (Available to all) === -->
        <div class="bg-white rounded-3xl p-8 border-2 border-transparent hover:border-purple-500 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer relative overflow-hidden" @click="router.push('/profile')">
          <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
            <UserIcon class="w-8 h-8" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Profile Settings</h3>
          <p class="text-slate-500 font-semibold mb-8">Update your personal information, bio, and upload your latest resume.</p>
          
          <div class="flex items-center text-purple-600 font-bold gap-2 group-hover:gap-4 transition-all">
            Edit Profile <ArrowRight class="w-5 h-5" />
          </div>
        </div>

      </div>
    </main>
  </div>
</template>