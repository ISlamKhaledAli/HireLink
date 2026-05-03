<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { 
  Briefcase, 
  User as UserIcon, 
  ShieldCheck,
  Search,
  ArrowRight,
  LogOut,
  Sparkles
} from 'lucide-vue-next'

const authStore = useAuthStore()
const router = useRouter()

// جلب الصلاحيات للتحكم في عرض الكروت
const isAdmin = computed(() => authStore.roles.includes('admin'))
const isCandidate = computed(() => authStore.roles.includes('candidate'))
// في حال وجود employer لاحقاً
const isEmployer = computed(() => authStore.roles.includes('employer'))

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex flex-col relative overflow-hidden">
    <!-- Background Gradients (Aesthetic) -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-blue-50/50 to-transparent -z-10"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-secondary/5 rounded-full blur-3xl -z-10"></div>

    <!-- Clean Minimal Header -->
    <header class="px-8 py-5 flex justify-between items-center z-10 max-w-7xl mx-auto w-full">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center font-black text-xl shadow-md">H</div>
        <h1 class="text-2xl font-black text-primary tracking-tight">HireLink</h1>
      </div>
      
      <div class="flex items-center gap-4 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
        <div class="text-right hidden sm:block px-2">
          <p class="text-sm font-bold text-primary leading-tight">{{ authStore.user?.name || 'User' }}</p>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ authStore.roles.join(', ') || 'Standard User' }}</p>
        </div>
        <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold border border-primary/20">
           {{ (authStore.user?.name || 'U')[0].toUpperCase() }}
        </div>
        <button @click="handleLogout" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all ml-1" title="Logout">
          <LogOut class="w-4 h-4" />
        </button>
      </div>
    </header>

    <!-- Main Portal Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-6 flex flex-col items-center justify-center z-10 mt-10 md:mt-0">
      
      <div class="text-center mb-16">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 text-blue-700 font-bold text-xs uppercase tracking-widest mb-6 border border-blue-100">
          <Sparkles class="w-4 h-4" />
          Workspace Portal
        </div>
        <h2 class="text-4xl md:text-5xl font-black text-primary mb-4 tracking-tight">Welcome back, {{ authStore.user?.name?.split(' ')[0] || 'there' }}!</h2>
        <p class="text-slate-500 font-semibold text-lg max-w-xl mx-auto">Select a module below to access your dedicated workspace and tools.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-4xl">
        
        <!-- === ADMIN PORTAL CARD === -->
        <div v-if="isAdmin" @click="router.push('/admin')" class="bg-white rounded-3xl p-8 border border-slate-100 hover:border-primary shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-14 h-14 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
            <ShieldCheck class="w-7 h-7" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Admin Dashboard</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Manage system users, approve pending job listings, and monitor platform analytics.</p>
          
          <div class="flex items-center text-primary font-black text-sm uppercase tracking-widest gap-2 group-hover:gap-4 transition-all">
            Enter Workspace <ArrowRight class="w-4 h-4" />
          </div>
        </div>

        <!-- === CANDIDATE PORTAL CARD === -->
        <div v-if="isCandidate" @click="router.push('/candidate/applications')" class="bg-white rounded-3xl p-8 border border-slate-100 hover:border-secondary shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          
          <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
            <Briefcase class="w-7 h-7" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">My Applications</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Track your application statuses, review employer feedback, and manage your job hunt.</p>
          
          <div class="flex items-center text-secondary font-black text-sm uppercase tracking-widest gap-2 group-hover:gap-4 transition-all">
            View Progress <ArrowRight class="w-4 h-4" />
          </div>
        </div>

        <!-- === EMPLOYER PORTAL CARD (Placeholder for future) === -->
        <div v-if="isEmployer" @click="router.push('/employer/jobs')" class="bg-white rounded-3xl p-8 border border-slate-100 hover:border-emerald-500 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
          <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
            <Briefcase class="w-7 h-7" />
          </div>
          <h3 class="text-2xl font-black text-primary mb-2">Employer Hub</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Post new job openings, review candidate resumes, and manage your hiring pipeline.</p>
          <div class="flex items-center text-emerald-600 font-black text-sm uppercase tracking-widest gap-2 group-hover:gap-4 transition-all">
            Manage Hiring <ArrowRight class="w-4 h-4" />
          </div>
        </div>

        <!-- === EXPLORE JOBS CARD (Available to all) === -->
        <div @click="router.push('/jobs')" class="bg-white rounded-3xl p-8 border border-slate-100 hover:border-blue-400 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer relative overflow-hidden" :class="{ 'md:col-span-2': (!isAdmin && !isCandidate && !isEmployer) }">
          <div class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
            <Search class="w-7 h-7" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Explore Opportunities</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Browse our global job board, filter by salary or location, and discover your next role.</p>
          
          <div class="flex items-center text-slate-400 font-black text-sm uppercase tracking-widest gap-2 group-hover:text-blue-600 group-hover:gap-4 transition-all">
            Start Searching <ArrowRight class="w-4 h-4" />
          </div>
        </div>

        <!-- === PROFILE SETTINGS CARD === -->
        <div @click="router.push('/profile')" class="bg-white rounded-3xl p-8 border border-slate-100 hover:border-purple-400 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer relative overflow-hidden" :class="{ 'hidden': !authStore.user }">
          <div class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mb-6 shadow-inner group-hover:bg-purple-100 group-hover:text-purple-600 transition-colors">
            <UserIcon class="w-7 h-7" />
          </div>
          
          <h3 class="text-2xl font-black text-primary mb-2">Account Settings</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Update your bio, upload your latest resume, and manage your personal details.</p>
          
          <div class="flex items-center text-slate-400 font-black text-sm uppercase tracking-widest gap-2 group-hover:text-purple-600 group-hover:gap-4 transition-all">
            Edit Profile <ArrowRight class="w-4 h-4" />
          </div>
        </div>

      </div>
    </main>
  </div>
</template>