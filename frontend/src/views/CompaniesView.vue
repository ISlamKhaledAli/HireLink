<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { Building2, MapPin, Briefcase, Search, Loader2, Globe, Users } from 'lucide-vue-next'

const companies = ref([])
const loading = ref(true)
const searchQuery = ref('')

onMounted(async () => {
  try {
    const response = await api.get('/companies')
    companies.value = response.data
  } catch (err) {
    console.error('Failed to fetch companies:', err)
  } finally {
    loading.value = false
  }
})

function filteredCompanies() {
  if (!searchQuery.value) return companies.value
  return companies.value.filter(c => 
    c.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
}
</script>

<template>
  <div class="bg-[#FAFAFC] min-h-screen font-display">
    <!-- Hero Section -->
    <div class="bg-primary text-white py-24 px-6 overflow-hidden relative">
      <div class="max-w-7xl mx-auto relative z-10">
        <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tight">Browse top <span class="text-secondary">companies</span></h1>
        <p class="text-blue-100 text-lg font-bold max-w-2xl mb-10">
          Discover the world's most innovative workplaces. From tech giants to disruptive startups, find where you belong.
        </p>
        
        <div class="relative max-w-xl">
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5" />
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search company by name..." 
            class="w-full bg-white text-slate-900 py-5 pl-12 pr-6 rounded-2xl border-none focus:ring-4 focus:ring-secondary/30 transition-all font-bold shadow-xl"
          />
        </div>
      </div>
      
      <!-- Decorative circles -->
      <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-secondary opacity-10 rounded-full blur-3xl"></div>
      <div class="absolute right-40 top-10 w-64 h-64 bg-blue-400 opacity-10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-20">
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <Loader2 class="w-12 h-12 text-primary animate-spin mb-4" />
        <p class="text-slate-500 font-bold">Discovering employers...</p>
      </div>

      <div v-else-if="filteredCompanies().length === 0" class="text-center py-20">
        <Building2 class="w-20 h-20 text-slate-200 mx-auto mb-6" />
        <h3 class="text-2xl font-black text-primary mb-2">No companies found</h3>
        <p class="text-slate-500 font-bold">Try adjusting your search query.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="company in filteredCompanies()" 
          :key="company.id"
          class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group"
        >
          <div class="flex items-center gap-5 mb-8">
            <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center group-hover:bg-primary/5 transition-colors">
              <Building2 class="w-8 h-8 text-slate-400 group-hover:text-primary transition-colors" />
            </div>
            <div>
              <h2 class="text-xl font-black text-primary group-hover:text-secondary transition-colors">{{ company.name }}</h2>
              <div class="flex items-center gap-1.5 text-slate-400 text-xs font-black uppercase tracking-widest mt-1">
                <MapPin class="w-3 h-3" />
                {{ company.location || 'Global' }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <div class="flex items-center gap-2 text-slate-400 mb-1">
                <Briefcase class="w-4 h-4" />
                <span class="text-[10px] font-black uppercase tracking-widest">Open Jobs</span>
              </div>
              <p class="text-2xl font-black text-primary">{{ company.jobs_count || 0 }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <div class="flex items-center gap-2 text-slate-400 mb-1">
                <Users class="w-4 h-4" />
                <span class="text-[10px] font-black uppercase tracking-widest">Industry</span>
              </div>
              <p class="text-sm font-black text-primary truncate">{{ company.industry || 'Tech' }}</p>
            </div>
          </div>

          <button class="w-full py-4 bg-primary/5 text-primary border border-primary/10 rounded-2xl font-black text-sm hover:bg-primary hover:text-white transition-all flex items-center justify-center gap-2">
            View Career Page
            <Globe class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
