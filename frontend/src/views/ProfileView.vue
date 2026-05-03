<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { updateProfileApi } from '@/services/profile'
import { 
  LayoutDashboard, 
  Briefcase, 
  User as UserIcon, 
  Mail, 
  Phone, 
  Link as LinkIcon, 
  FileText, 
  UploadCloud,
  Save,
  Loader2,
  AlertCircle
} from 'lucide-vue-next'

const authStore = useAuthStore()

// حالة الفورم (Form State)
const form = ref({
  name: '',
  phone: '',
  bio: '',
  linkedin_id: ''
})

const resumeFile = ref(null)
const resumeError = ref('')
const isSubmitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// ملء البيانات الحالية عند تحميل الصفحة
onMounted(() => {
  if (authStore.user) {
    form.value.name = authStore.user.name || ''
    form.value.phone = authStore.user.phone || ''
    form.value.bio = authStore.user.bio || ''
    form.value.linkedin_id = authStore.user.linkedin_id || ''
  }
})

// التعامل مع اختيار ملف السيرة الذاتية
const handleFileUpload = (event) => {
  resumeError.value = ''
  const file = event.target.files[0]
  
  if (!file) return

  // التحقق من نوع الملف (PDF فقط)
  if (file.type !== 'application/pdf') {
    resumeError.value = 'Only PDF files are allowed.'
    event.target.value = ''
    return
  }

  // التحقق من الحجم (الحد الأقصى 2MB)
  const maxSize = 2 * 1024 * 1024
  if (file.size > maxSize) {
    resumeError.value = 'File size must not exceed 2MB.'
    event.target.value = ''
    return
  }

  resumeFile.value = file
}

// إرسال البيانات
const saveProfile = async () => {
  isSubmitting.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('phone', form.value.phone)
    formData.append('bio', form.value.bio)
    formData.append('linkedin_id', form.value.linkedin_id)
    
    if (resumeFile.value) {
      formData.append('resume', resumeFile.value)
    }

    // استدعاء الـ API (قد يعطي 404 حالياً حتى يتم بناء الباك إند)
    await updateProfileApi(formData)
    
    // تحديث بيانات المستخدم في الـ Store
    await authStore.fetchUser()
    
    successMessage.value = 'Profile updated successfully!'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to update profile. Endpoint might not exist yet.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="bg-[#faf9fc] text-slate-900 font-body min-h-screen flex">
    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col z-40">
      <div class="text-xl font-black text-primary px-6 py-8 tracking-tight">
        HireLink
      </div>
      <nav class="flex flex-col h-full space-y-1">
        <router-link to="/" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <LayoutDashboard class="w-5 h-5" />
          Dashboard
        </router-link>
        
        <router-link to="/candidate/applications" class="flex items-center gap-3 text-slate-500 px-6 py-3 hover:bg-slate-50 transition-colors duration-200 font-bold text-sm">
          <Briefcase class="w-5 h-5" />
          My Applications
        </router-link>

        <!-- Active State for Profile -->
        <router-link to="/profile" class="flex items-center gap-3 bg-primary/5 text-primary border-r-4 border-primary px-6 py-3 font-black text-sm">
          <UserIcon class="w-5 h-5" />
          Profile
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 min-h-screen p-8 max-w-5xl">
      <header class="mb-8">
        <h1 class="text-3xl font-black text-primary tracking-tight">Profile Settings</h1>
        <p class="text-sm font-bold text-slate-500 mt-2">Manage your personal information and resume.</p>
      </header>

      <!-- Status Messages -->
      <div v-if="successMessage" class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-200 font-bold text-sm flex items-center gap-2">
        <AlertCircle class="w-5 h-5" /> {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 font-bold text-sm flex items-center gap-2">
        <AlertCircle class="w-5 h-5" /> {{ errorMessage }}
      </div>

      <form @submit.prevent="saveProfile" class="space-y-8">
        <!-- Personal Information Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
          <div class="flex items-center gap-6 mb-8 pb-8 border-b border-slate-100">
            <div class="w-20 h-20 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-inner">
               <img v-if="authStore.user?.avatar" :src="authStore.user.avatar" class="w-full h-full object-cover" />
               <UserIcon v-else class="w-8 h-8" />
            </div>
            <div>
              <h3 class="text-lg font-black text-primary">{{ authStore.user?.name || 'Your Name' }}</h3>
              <p class="text-sm font-bold text-slate-400 mt-1">{{ authStore.user?.email }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">Full Name</label>
              <div class="relative">
                <UserIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input v-model="form.name" type="text" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" />
              </div>
            </div>

            <!-- Email (Readonly) -->
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">Email Address (Read-only)</label>
              <div class="relative">
                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input :value="authStore.user?.email" type="email" disabled class="w-full pl-10 pr-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 cursor-not-allowed" />
              </div>
            </div>

            <!-- Phone -->
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">Phone Number</label>
              <div class="relative">
                <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input v-model="form.phone" type="tel" placeholder="+1 (555) 000-0000" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" />
              </div>
            </div>

            <!-- LinkedIn URL -->
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">LinkedIn Profile</label>
              <div class="relative">
                <LinkIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input v-model="form.linkedin_id" type="url" placeholder="https://linkedin.com/in/username" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" />
              </div>
            </div>

            <!-- Bio -->
            <div class="space-y-2 md:col-span-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">Professional Bio</label>
              <textarea v-model="form.bio" rows="4" placeholder="Tell companies about your experience and skills..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none"></textarea>
            </div>
          </div>
        </div>

        <!-- Resume / CV Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
          <h3 class="text-lg font-black text-primary mb-6">Resume / CV</h3>
          
          <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:bg-slate-50 transition-colors relative group">
            <input type="file" accept="application/pdf" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
            
            <div class="flex flex-col items-center justify-center space-y-3">
              <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                <UploadCloud class="w-6 h-6" />
              </div>
              <div v-if="!resumeFile">
                <p class="text-sm font-black text-primary">Click to upload or drag and drop</p>
                <p class="text-xs font-bold text-slate-400 mt-1">PDF only (Max 2MB)</p>
              </div>
              <div v-else class="flex items-center gap-2 text-primary font-black text-sm">
                <FileText class="w-4 h-4" />
                {{ resumeFile.name }}
              </div>
            </div>
          </div>
          <p v-if="resumeError" class="text-red-500 text-xs font-bold mt-2">{{ resumeError }}</p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button type="submit" :disabled="isSubmitting" class="bg-secondary text-white px-8 py-3 rounded-xl font-black text-sm hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-secondary/20 flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
            <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
            <Save v-else class="w-4 h-4" />
            {{ isSubmitting ? 'Saving Changes...' : 'Save Profile' }}
          </button>
        </div>
      </form>
    </main>
  </div>
</template>