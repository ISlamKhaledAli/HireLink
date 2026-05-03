<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import { 
  LayoutDashboard, 
  Briefcase, 
  User as UserIcon, 
  Settings,
  ShieldCheck,
  Search,
  Bell,
  LogOut
} from 'lucide-vue-next'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const isAdmin = computed(() => authStore.roles.includes('admin'))
const isCandidate = computed(() => authStore.roles.includes('candidate'))

// دالة لمعرفة ما إذا كان الرابط هو الرابط النشط حالياً لتلوينه
const isActive = (path) => route.path === path

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex">
    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col z-40 shadow-sm">
      <div class="flex items-center gap-3 px-6 py-8 cursor-pointer" @click="router.push('/')">
        <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-black">H</div>
        <div class="text-xl font-black text-primary tracking-tight">
          HireLink 
          <span v-if="isAdmin" class="text-secondary text-sm ml-1">Admin</span>
        </div>
      </div>
      
      <nav class="flex flex-col h-full space-y-1 mt-2">
        <!-- 🔹 روابط الـ Admin -->
        <template v-if="isAdmin">
          <router-link to="/admin" class="flex items-center gap-3 px-6 py-3 font-bold text-sm transition-colors duration-200"
            :class="isActive('/admin') ? 'bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-500 hover:bg-slate-50'">
            <LayoutDashboard class="w-5 h-5" /> Overview
          </router-link>
          
          <router-link to="/admin/jobs" class="flex items-center gap-3 px-6 py-3 font-bold text-sm transition-colors duration-200"
            :class="isActive('/admin/jobs') ? 'bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-500 hover:bg-slate-50'">
            <Briefcase class="w-5 h-5" /> Manage Jobs
          </router-link>
        </template>

        <!-- 🔹 روابط الـ Candidate -->
        <template v-if="isCandidate">
          <router-link to="/candidate/applications" class="flex items-center gap-3 px-6 py-3 font-bold text-sm transition-colors duration-200"
            :class="isActive('/candidate/applications') ? 'bg-orange-50 text-secondary border-r-4 border-secondary' : 'text-slate-500 hover:bg-slate-50'">
            <Briefcase class="w-5 h-5" /> My Applications
          </router-link>
        </template>

        <!-- 🔹 روابط مشتركة (للجميع) -->
        <div class="my-4 border-t border-slate-100 mx-4"></div>

        <router-link to="/jobs" class="flex items-center gap-3 px-6 py-3 font-bold text-sm transition-colors duration-200"
          :class="isActive('/jobs') ? 'bg-emerald-50 text-emerald-700 border-r-4 border-emerald-500' : 'text-slate-500 hover:bg-slate-50'">
          <Search class="w-5 h-5" /> Find Jobs
        </router-link>

        <router-link to="/profile" class="flex items-center gap-3 px-6 py-3 font-bold text-sm transition-colors duration-200"
          :class="isActive('/profile') ? 'bg-purple-50 text-purple-700 border-r-4 border-purple-500' : 'text-slate-500 hover:bg-slate-50'">
          <UserIcon class="w-5 h-5" /> Profile Settings
        </router-link>
      </nav>
      
      <!-- User Profile Snippet in Sidebar -->
      <div class="mt-auto p-6 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary font-black border border-slate-200 shadow-sm overflow-hidden shrink-0">
             <img v-if="authStore.user?.avatar" :src="authStore.user.avatar" class="w-full h-full object-cover" />
             <span v-else>{{ (authStore.user?.name || 'U')[0].toUpperCase() }}</span>
          </div>
          <div class="overflow-hidden">
            <div class="font-bold text-primary text-sm truncate">{{ authStore.user?.name || 'User' }}</div>
            <div class="text-slate-500 text-xs font-semibold truncate">{{ authStore.roles[0] || 'Member' }}</div>
          </div>
        </div>
        <button @click="handleLogout" class="w-full flex items-center justify-center gap-2 py-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg text-sm font-bold transition-colors">
          <LogOut class="w-4 h-4" /> Logout
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <!-- استخدام الـ Slot أو تمرير اسم الصفحة -->
        <h1 class="text-2xl font-black text-primary tracking-tight capitalize">
          {{ route.meta.title || 'Dashboard' }}
        </h1>
        
        <div class="flex items-center gap-6">
          <button class="relative text-slate-400 hover:text-primary transition-colors">
            <Bell class="w-6 h-6" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-secondary rounded-full border-2 border-white"></span>
          </button>
        </div>
      </header>

      <!-- 🔹 المحتوى المتغير سيتم حقنه هنا 🔹 -->
      <div class="p-8 max-w-7xl mx-auto w-full">
        <slot />
      </div>
    </main>
  </div>
</template>