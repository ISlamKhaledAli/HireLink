<script setup>
import { useRouter } from 'vue-router'
import { 
  Building2, 
  MapPin, 
  Calendar, 
  Tag, 
  ArrowRight,
  Briefcase,
  DollarSign
} from 'lucide-vue-next'

const props = defineProps({
  job: {
    type: Object,
    required: true
  }
})

const router = useRouter()

function goToDetails() {
  router.push(`/jobs/${props.job.id}`)
}

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric'
  }).format(date)
}

function formatSalary(amount) {
  if (!amount) return '0'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0
  }).format(amount / 1000) + 'k'
}
</script>

<template>
  <div 
    @click="goToDetails"
    class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 hover:scale-[1.01] hover:border-primary/20 active:scale-[0.99] transition-all duration-300 flex flex-col md:flex-row gap-8 group cursor-pointer relative overflow-hidden"
  >
    <!-- Logo Container -->
    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100 group-hover:bg-primary/5 group-hover:border-primary/10 transition-all duration-300">
      <img 
        v-if="job.logo" 
        :src="job.logo" 
        class="w-10 h-10 object-contain rounded" 
        alt="Logo"
      />
      <Building2 v-else class="w-8 h-8 text-slate-400 group-hover:text-primary transition-colors" />
    </div>

    <!-- Content -->
    <div class="flex-1">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
          <h3 class="text-2xl font-black text-slate-900 group-hover:text-primary transition-colors duration-300 tracking-tight">
            {{ job.title }}
          </h3>
          <p class="text-primary font-bold text-sm mt-1 flex items-center gap-1.5">
            <Building2 class="w-4 h-4" />
            {{ job.company || job.user?.name }}
          </p>
        </div>
        <div class="flex flex-col items-start sm:items-end">
          <span class="text-xl font-black text-secondary flex items-center gap-1">
            {{ job.salary_range || 'Negotiable' }}
          </span>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Expected Annual Salary</span>
        </div>
      </div>

      <div class="flex flex-wrap gap-x-8 gap-y-3 mb-6">
        <div class="flex items-center gap-2 text-slate-500 font-bold text-xs uppercase tracking-wider">
          <MapPin class="w-4 h-4 text-slate-400" />
          {{ job.location }}
        </div>
        <div class="flex items-center gap-2 text-slate-500 font-bold text-xs uppercase tracking-wider">
          <Calendar class="w-4 h-4 text-slate-400" />
          Posted {{ formatDate(job.created_at) }}
        </div>
        <div class="flex items-center gap-2 text-slate-500 font-bold text-xs uppercase tracking-wider">
          <Tag class="w-4 h-4 text-slate-400" />
          {{ job.category?.name || 'Uncategorized' }}
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div 
          class="flex items-center gap-2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all duration-300"
          :class="{
            'bg-emerald-50 text-emerald-700 border-emerald-100 group-hover:bg-emerald-100': job.work_type === 'remote',
            'bg-blue-50 text-blue-700 border-blue-100 group-hover:bg-blue-100': job.work_type === 'onsite',
            'bg-orange-50 text-orange-700 border-orange-100 group-hover:bg-orange-100': job.work_type === 'hybrid',
            'bg-slate-100 text-slate-700 border-slate-200': !['remote', 'onsite', 'hybrid'].includes(job.work_type)
          }"
        >
          <div class="w-2 h-2 rounded-full" :class="{
            'bg-emerald-500': job.work_type === 'remote',
            'bg-blue-500': job.work_type === 'onsite',
            'bg-orange-500': job.work_type === 'hybrid',
            'bg-slate-500': !['remote', 'onsite', 'hybrid'].includes(job.work_type)
          }"></div>
          {{ job.work_type }}
        </div>
        <div class="flex items-center gap-2 px-4 py-1.5 bg-slate-50 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100 group-hover:bg-slate-100">
          <Briefcase class="w-3 h-3" />
          Full-time
        </div>
      </div>
    </div>

    <!-- Action -->
    <div class="flex items-center md:items-end justify-end">
      <button 
        class="w-full md:w-auto bg-primary text-white px-8 py-4 rounded-2xl font-black hover:scale-105 active:scale-95 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-3 group/btn"
      >
        View Details
        <ArrowRight class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" />
      </button>
    </div>
  </div>
</template>
