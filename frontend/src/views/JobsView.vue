<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useJobStore } from '@/stores/jobStore'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import JobCard from '@/components/JobCard.vue'
import BaseInput from '@/components/BaseInput.vue'
import { 
  Search, 
  MapPin, 
  ChevronDown, 
  LogOut, 
  User as UserIcon, 
  LayoutDashboard,
  DollarSign,
  Loader2,
  Briefcase,
  Calendar,
  Filter
} from 'lucide-vue-next'

const jobStore = useJobStore()
const authStore = useAuthStore()
const router = useRouter()
const categories = ref([])
const isDropdownOpen = ref(false)

const filters = ref({
  keyword: '',
  location: '',
  category: '',
  salary_min: '',
  salary_max: '',
  work_type: [],
  date_posted: '',
})

const isLoggedIn = computed(() => !!authStore.token && !!authStore.user)

onMounted(async () => {
  jobStore.fetchJobs()
  fetchCategories()
})

async function fetchCategories() {
  try {
    const response = await api.get('/categories')
    categories.value = response.data
  } catch (err) {
    console.error('Failed to fetch categories:', err)
  }
}

function search(page = 1) {
  const searchFilters = { 
    ...filters.value,
    page: page
  }
  
  // Format work_type as comma separated string for backend whereIn logic
  if (Array.isArray(searchFilters.work_type) && searchFilters.work_type.length > 0) {
    searchFilters.work_type = searchFilters.work_type.join(',')
  } else {
    delete searchFilters.work_type
  }

  jobStore.fetchJobs(searchFilters)
}

function clearFilters() {
  filters.value = {
    keyword: '',
    location: '',
    category: '',
    salary_min: '',
    salary_max: '',
    work_type: [],
    date_posted: '',
  }
  jobStore.fetchJobs()
}

// Watch filters for automatic updates (Requirement 3: Reactivity)
// We use a deep watch to catch changes in work_type array
watch(
  () => [filters.value.category, filters.value.work_type, filters.value.date_posted],
  () => {
    search()
  },
  { deep: true }
)

async function handleLogout() {
  await authStore.logout()
  isDropdownOpen.value = false
  router.push('/login')
}
</script>

<template>
  <div class="bg-[#FAFAFC] font-body text-slate-900 min-h-screen flex flex-col">
    <!-- TopNavBar Component -->
    <nav class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 shadow-sm font-display antialiased">
      <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
        <div class="flex items-center gap-10">
          <router-link to="/" class="text-2xl font-black tracking-tight text-primary hover:opacity-80 transition-all">HireLink</router-link>
          <div class="hidden md:flex gap-6">
            <router-link to="/jobs" class="text-primary font-bold border-b-2 border-secondary pb-1">Find Jobs</router-link>
            <a class="text-slate-500 font-semibold hover:text-primary transition-all duration-200" href="#">Companies</a>
            <a class="text-slate-500 font-semibold hover:text-primary transition-all duration-200" href="#">Pricing</a>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <!-- Using BaseInput for Navbar Search -->
          <div class="hidden lg:block w-64">
            <BaseInput 
              v-model="filters.keyword" 
              :icon="Search" 
              placeholder="Search roles..." 
              rounded="rounded-full"
              @enter="search"
            />
          </div>

          <!-- Auth State -->
          <div v-if="isLoggedIn" class="relative">
            <button 
              @click="isDropdownOpen = !isDropdownOpen"
              class="flex items-center gap-2 p-1 hover:bg-slate-50 rounded-full transition-all border border-transparent hover:border-slate-100"
            >
              <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold border border-primary/20 overflow-hidden">
                <img v-if="authStore.user.avatar" :src="authStore.user.avatar" class="w-full h-full object-cover" />
                <UserIcon v-else class="w-5 h-5" />
              </div>
              <ChevronDown class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': isDropdownOpen }" />
            </button>

            <div v-if="isDropdownOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-[60] overflow-hidden animate-in fade-in zoom-in duration-200">
              <div class="px-4 py-3 border-b border-slate-50 mb-1 bg-slate-50/50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Signed in as</p>
                <p class="text-xs font-bold text-slate-900 truncate">{{ authStore.user.email }}</p>
              </div>
              <router-link to="/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary">
                <LayoutDashboard class="w-4 h-4" /> Dashboard
              </router-link>
              <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">
                <LogOut class="w-4 h-4" /> Logout
              </button>
            </div>
          </div>

          <template v-else>
            <router-link to="/login" class="text-slate-600 font-bold hover:text-primary px-4 py-2 transition-all">Login</router-link>
            <button class="bg-secondary text-white px-6 py-2.5 rounded-xl font-bold shadow-md shadow-secondary/20 hover:scale-105 active:scale-95 transition-all">Post a Job</button>
          </template>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto w-full px-6 py-10 flex flex-col lg:flex-row gap-10">
      <!-- Sidebar Filters -->
      <aside class="w-72 hidden lg:flex flex-col gap-8 shrink-0">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-black text-primary tracking-tight">Filters</h2>
            <button @click="clearFilters" class="text-xs font-black text-secondary hover:text-orange-700 transition-colors bg-secondary/5 px-3 py-1.5 rounded-full cursor-pointer">Clear all</button>
          </div>

          <div class="flex flex-col gap-8">
            <!-- Location Filter using BaseInput -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Location</label>
              <BaseInput 
                v-model="filters.location" 
                :icon="MapPin" 
                placeholder="City or region"
                @enter="search"
              />
            </div>

            <!-- Work Type -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Work Type</label>
              <div class="space-y-3">
                <label v-for="type in ['remote', 'onsite', 'hybrid']" :key="type" class="flex items-center gap-3 cursor-pointer group">
                  <div class="relative flex items-center justify-center">
                    <input 
                      type="checkbox" 
                      :value="type" 
                      v-model="filters.work_type"
                      class="peer w-5 h-5 rounded-lg border-2 border-slate-200 text-primary focus:ring-0 transition-all checked:border-primary cursor-pointer appearance-none bg-white"
                    />
                    <div class="absolute w-2.5 h-2.5 bg-primary rounded-[2px] opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></div>
                  </div>
                  <span class="text-sm font-bold text-slate-600 group-hover:text-primary capitalize transition-colors">{{ type }}</span>
                </label>
              </div>
            </div>

            <!-- Salary Range using BaseInput -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Salary Range</label>
              <div class="flex items-center gap-2">
                <BaseInput 
                  v-model="filters.salary_min" 
                  :icon="DollarSign" 
                  type="number" 
                  placeholder="Min" 
                  @enter="search"
                />
                <span class="text-slate-300 font-bold">—</span>
                <BaseInput 
                  v-model="filters.salary_max" 
                  :icon="DollarSign" 
                  type="number" 
                  placeholder="Max" 
                  @enter="search"
                />
              </div>
            </div>

            <!-- Category -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Job Category</label>
              <div class="space-y-2 max-h-56 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200">
                <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group py-1">
                  <input 
                    type="radio" 
                    :value="cat.id" 
                    v-model="filters.category"
                    class="w-5 h-5 border-2 border-slate-200 text-primary focus:ring-0 transition-all cursor-pointer"
                  />
                  <span class="text-sm font-bold text-slate-600 group-hover:text-primary transition-colors">{{ cat.name }}</span>
                </label>
              </div>
            </div>

            <!-- Date Posted -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Date Posted</label>
              <div class="flex flex-col gap-2">
                <button 
                  v-for="d in [{val: 'today', label: 'Last 24 hours'}, {val: 'week', label: 'Last 7 days'}, {val: 'month', label: 'Last 30 days'}]" 
                  :key="d.val"
                  @click="filters.date_posted = filters.date_posted === d.val ? '' : d.val"
                  class="text-left px-4 py-3 text-xs font-bold transition-all rounded-xl border-2"
                  :class="filters.date_posted === d.val ? 'border-primary bg-primary/5 text-primary' : 'border-transparent bg-slate-50 text-slate-500 hover:bg-slate-100'"
                >
                  {{ d.label }}
                </button>
              </div>
            </div>

            <button 
              @click="search"
              class="w-full bg-primary text-white py-4 rounded-xl font-black hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-primary/20 mt-4 uppercase tracking-widest text-xs cursor-pointer"
            >
              Search Jobs
            </button>
          </div>
        </div>
      </aside>

      <!-- Main Content (Job Listings) -->
      <section class="flex-1 flex flex-col gap-6">
        <!-- Header & Sorting -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
          <div>
            <h1 class="text-3xl font-black text-primary tracking-tight">
              <template v-if="jobStore.loading">
                <span class="flex items-center gap-3">
                  <Loader2 class="w-6 h-6 animate-spin" />
                  Finding roles...
                </span>
              </template>
              <template v-else>
                {{ jobStore.pagination?.total || jobStore.jobs.length }} Opportunities
              </template>
            </h1>
            <p class="text-slate-500 font-bold text-sm">Showing the latest positions available</p>
          </div>
          <div class="flex items-center gap-3 bg-white border border-slate-200 p-1.5 rounded-xl shadow-sm">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-2">Sort:</span>
            <select class="bg-transparent border-none focus:ring-0 text-sm font-bold text-slate-700 py-1 pl-2 pr-8 cursor-pointer">
              <option>Newest First</option>
              <option>Highest Salary</option>
            </select>
          </div>
        </div>

        <!-- Job Card List -->
        <div class="grid gap-5">
          <div v-if="jobStore.loading" class="flex flex-col items-center justify-center py-32 gap-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
             <Loader2 class="w-12 h-12 text-primary animate-spin" />
             <p class="text-slate-400 font-black uppercase tracking-widest text-xs">Accessing Job Bank...</p>
          </div>

          <JobCard 
            v-for="job in jobStore.jobs" 
            :key="job.id" 
            :job="job" 
          />

          <!-- Empty State -->
          <div v-if="!jobStore.loading && jobStore.jobs.length === 0" class="flex flex-col items-center justify-center py-32 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-center px-10">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
              <Briefcase class="w-10 h-10 text-slate-300" />
            </div>
            <h3 class="text-2xl font-black text-primary mb-2">No matching jobs</h3>
            <p class="text-slate-500 font-bold max-w-sm mb-8">Try adjusting your filters or search criteria to see more opportunities.</p>
            <button @click="clearFilters" class="bg-primary text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 cursor-pointer">Clear All Filters</button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="jobStore.pagination && jobStore.pagination.last_page > 1" class="flex items-center justify-center gap-3 mt-10 py-6 border-t border-slate-200">
          <button 
            @click="search(jobStore.pagination.current_page - 1)"
            :disabled="jobStore.pagination.current_page === 1"
            class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined rotate-90">expand_more</span>
          </button>
          <div class="flex gap-2">
            <button 
              v-for="p in jobStore.pagination.last_page" 
              :key="p"
              @click="search(p)"
              class="w-11 h-11 flex items-center justify-center rounded-xl font-black text-sm transition-all shadow-sm"
              :class="jobStore.pagination.current_page === p ? 'bg-primary text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
            >
              {{ p }}
            </button>
          </div>
          <button 
            @click="search(jobStore.pagination.current_page + 1)"
            :disabled="jobStore.pagination.current_page === jobStore.pagination.last_page"
            class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined -rotate-90">expand_more</span>
          </button>
        </div>
      </section>
    </main>

    <!-- Footer Component -->
    <footer class="w-full mt-auto py-20 bg-white border-t border-slate-100 font-display">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-16">
          <div class="flex flex-col gap-6">
            <div class="text-2xl font-black text-primary tracking-tight">HireLink</div>
            <p class="text-slate-500 font-bold leading-relaxed text-sm">
              Connecting the world's best talent with the most innovative companies on the planet. Built for modern professionals.
            </p>
            <div class="flex gap-4">
              <a v-for="i in 3" :key="i" href="#" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-primary transition-all">
                <div class="w-5 h-5 bg-current rounded-sm opacity-20"></div>
              </a>
            </div>
          </div>
          <div class="flex flex-col gap-6">
            <h4 class="font-black text-primary uppercase tracking-widest text-[11px]">Platform</h4>
            <ul class="flex flex-col gap-4 text-slate-500 font-bold text-sm">
              <li><a class="hover:text-primary transition-colors" href="#">Find Jobs</a></li>
              <li><a class="hover:text-primary transition-colors" href="#">Browse Categories</a></li>
              <li><a class="hover:text-primary transition-colors" href="#">Salaries</a></li>
            </ul>
          </div>
          <div class="flex flex-col gap-6">
            <h4 class="font-black text-primary uppercase tracking-widest text-[11px]">Company</h4>
            <ul class="flex flex-col gap-4 text-slate-500 font-bold text-sm">
              <li><a class="hover:text-primary transition-colors" href="#">About Us</a></li>
              <li><a class="hover:text-primary transition-colors" href="#">Legal</a></li>
              <li><a class="hover:text-primary transition-colors" href="#">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="flex flex-col gap-6">
            <h4 class="font-black text-primary uppercase tracking-widest text-[11px]">Support</h4>
            <ul class="flex flex-col gap-4 text-slate-500 font-bold text-sm">
              <li><a class="hover:text-primary transition-colors" href="#">Help Center</a></li>
              <li><a class="hover:text-primary transition-colors" href="#">Contact Us</a></li>
            </ul>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6 pt-10 border-t border-slate-50">
          <div class="text-slate-400 font-bold text-xs text-center sm:text-left">© 2026 HireLink Ecosystem. All rights reserved.</div>
          <div class="flex gap-8 text-slate-400 font-bold text-xs">
            <a href="#" class="hover:text-primary transition-colors">Cookies</a>
            <a href="#" class="hover:text-primary transition-colors">Terms</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
