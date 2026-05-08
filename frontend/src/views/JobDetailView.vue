<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getJob, getJobComments, postComment, deleteComment } from '@/services/jobs'
import { applyForJob } from '@/services/applications'
import {
  Building2, MapPin, Calendar, Tag, Briefcase, DollarSign,
  ArrowLeft, Loader2, Send, Trash2, Upload, CheckCircle2,
  Clock, Users, AlertCircle, MessageSquare, FileText
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const job = ref(null)
const loading = ref(true)
const error = ref(null)

// Apply form
const showApplyForm = ref(false)
const applyForm = ref({ email: '', phone: '', resume: null })
const applyLoading = ref(false)
const applyError = ref('')
const applySuccess = ref(false)
const alreadyApplied = ref(false)

// Comments
const comments = ref([])
const commentsLoading = ref(false)
const newComment = ref('')
const commentPosting = ref(false)
const deletingCommentId = ref(null)

const isLoggedIn = computed(() => !!authStore.token && !!authStore.user)
const isCandidate = computed(() => authStore.roles?.includes('candidate'))

onMounted(async () => {
  await loadJob()
  loadComments()
})

async function loadJob() {
  loading.value = true
  error.value = null
  try {
    const { data } = await getJob(route.params.id)
    job.value = data.data || data
  } catch (err) {
    console.error('Load job error:', err)
    error.value = err.response?.status === 404 ? 'Job not found.' : 'Failed to load job details.'
  } finally {
    loading.value = false
  }
}

async function loadComments() {
  commentsLoading.value = true
  try {
    const { data } = await getJobComments(route.params.id)
    comments.value = Array.isArray(data) ? data : []
  } catch (err) {
    console.error('Load comments error:', err)
  } finally {
    commentsLoading.value = false
  }
}

function openApplyForm() {
  if (!isLoggedIn.value) { router.push('/login'); return }
  if (!isCandidate.value) return
  applyForm.value.email = authStore.user?.email || ''
  applyForm.value.phone = authStore.user?.phone || ''
  applyForm.value.resume = null
  applyError.value = ''
  showApplyForm.value = true
}

function onFileChange(e) {
  const file = e.target.files?.[0]
  if (file) applyForm.value.resume = file
}

async function submitApplication() {
  applyLoading.value = true
  applyError.value = ''
  try {
    const fd = new FormData()
    fd.append('job_id', job.value.id)
    fd.append('email', applyForm.value.email)
    if (applyForm.value.phone) fd.append('phone', applyForm.value.phone)
    if (applyForm.value.resume) fd.append('resume', applyForm.value.resume)
    await applyForJob(fd)
    applySuccess.value = true
    alreadyApplied.value = true
    showApplyForm.value = false
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to submit application.'
    if (msg.includes('already applied')) { alreadyApplied.value = true; showApplyForm.value = false }
    applyError.value = msg
  } finally {
    applyLoading.value = false
  }
}

async function submitComment() {
  if (!newComment.value.trim()) return
  commentPosting.value = true
  try {
    await postComment({ content: newComment.value.trim(), job_id: job.value.id })
    newComment.value = ''
    await loadComments()
  } catch (err) {
    console.error('Post comment error:', err)
  } finally {
    commentPosting.value = false
  }
}

async function removeComment(id) {
  if (!confirm('Delete this comment?')) return
  deletingCommentId.value = id
  try {
    await deleteComment(id)
    comments.value = comments.value.filter(c => c.id !== id)
  } catch (err) {
    console.error('Delete comment error:', err)
  } finally {
    deletingCommentId.value = null
  }
}

function formatDate(d) {
  if (!d) return ''
  return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(d))
}

function timeAgo(d) {
  if (!d) return ''
  const diff = Date.now() - new Date(d).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  return days === 1 ? 'Yesterday' : `${days}d ago`
}
</script>

<template>
  <div class="bg-[#FAFAFC] font-body text-slate-900 min-h-screen">
    <!-- Nav -->
    <nav class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 shadow-sm">
      <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
        <div class="flex items-center gap-6">
          <router-link to="/" class="text-2xl font-black tracking-tight text-primary">HireLink</router-link>
          <router-link to="/jobs" class="text-slate-500 font-semibold hover:text-primary transition-colors hidden sm:block">← Back to Jobs</router-link>
        </div>
        <div class="flex items-center gap-3">
          <template v-if="isLoggedIn">
            <router-link to="/" class="text-sm font-bold text-slate-500 hover:text-primary">Dashboard</router-link>
          </template>
          <template v-else>
            <router-link to="/login" class="text-slate-600 font-bold hover:text-primary px-4 py-2">Login</router-link>
            <router-link to="/register" class="bg-secondary text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-secondary/20">Register</router-link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-4">
      <Loader2 class="w-12 h-12 text-primary animate-spin" />
      <p class="text-slate-400 font-black text-xs uppercase tracking-widest">Loading job details...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="max-w-2xl mx-auto px-6 py-20 text-center">
      <AlertCircle class="w-16 h-16 text-red-400 mx-auto mb-4" />
      <h2 class="text-2xl font-black text-primary mb-2">Oops!</h2>
      <p class="text-slate-500 font-semibold mb-6">{{ error }}</p>
      <button @click="loadJob" class="bg-primary text-white px-6 py-3 rounded-xl font-bold">Retry</button>
    </div>

    <!-- Job Content -->
    <main v-else-if="job" class="max-w-5xl mx-auto px-6 py-10">
      <!-- Back link mobile -->
      <router-link to="/jobs" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary mb-6 sm:hidden">
        <ArrowLeft class="w-4 h-4" /> Back to Jobs
      </router-link>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Header Card -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <div class="flex items-start gap-5 mb-6">
              <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center border border-primary/10 shrink-0">
                <Building2 class="w-8 h-8 text-primary" />
              </div>
              <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-black text-primary tracking-tight mb-1">{{ job.title }}</h1>
                <p class="text-primary/70 font-bold text-lg flex items-center gap-2">
                  <Building2 class="w-4 h-4" />
                  {{ job.user?.name || 'Company' }}
                </p>
              </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-6">
              <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border"
                :class="{
                  'bg-emerald-50 text-emerald-700 border-emerald-100': job.work_type === 'remote',
                  'bg-blue-50 text-blue-700 border-blue-100': job.work_type === 'onsite',
                  'bg-orange-50 text-orange-700 border-orange-100': job.work_type === 'hybrid',
                }">
                <div class="w-2 h-2 rounded-full" :class="{
                  'bg-emerald-500': job.work_type === 'remote',
                  'bg-blue-500': job.work_type === 'onsite',
                  'bg-orange-500': job.work_type === 'hybrid',
                }"></div>
                {{ job.work_type }}
              </span>
              <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-600 rounded-full text-xs font-black uppercase tracking-wider border border-slate-100">
                <Briefcase class="w-3 h-3" /> Full-time
              </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div class="flex items-center gap-2 text-sm"><MapPin class="w-4 h-4 text-slate-400 shrink-0" /><span class="font-bold text-slate-600 truncate">{{ job.location }}</span></div>
              <div class="flex items-center gap-2 text-sm"><DollarSign class="w-4 h-4 text-slate-400 shrink-0" /><span class="font-bold text-secondary">{{ job.salary_range || 'Negotiable' }}</span></div>
              <div class="flex items-center gap-2 text-sm"><Calendar class="w-4 h-4 text-slate-400 shrink-0" /><span class="font-bold text-slate-600">{{ formatDate(job.deadline) }}</span></div>
              <div class="flex items-center gap-2 text-sm"><Tag class="w-4 h-4 text-slate-400 shrink-0" /><span class="font-bold text-slate-600">{{ job.category?.name || 'General' }}</span></div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <h2 class="text-xl font-black text-primary mb-6 flex items-center gap-2">
              <FileText class="w-5 h-5" /> Job Description
            </h2>
            <div class="prose prose-slate max-w-none text-slate-700 font-medium leading-relaxed whitespace-pre-line">{{ job.description }}</div>
          </div>

          <!-- Comments Section -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <h2 class="text-xl font-black text-primary mb-6 flex items-center gap-2">
              <MessageSquare class="w-5 h-5" /> Discussion
              <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-xs font-black">{{ comments.length }}</span>
            </h2>

            <!-- Add Comment -->
            <div v-if="isLoggedIn" class="mb-8">
              <form @submit.prevent="submitComment" class="flex gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm border border-primary/20 shrink-0">
                  {{ (authStore.user?.name || 'U')[0].toUpperCase() }}
                </div>
                <div class="flex-1 flex gap-2">
                  <input v-model="newComment" type="text" placeholder="Add a comment..." :disabled="commentPosting"
                    class="flex-1 rounded-xl bg-slate-50 border border-slate-200 px-4 py-2.5 text-sm font-semibold outline-none focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 transition-all disabled:opacity-50" />
                  <button type="submit" :disabled="commentPosting || !newComment.trim()"
                    class="bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 disabled:opacity-40 transition-all flex items-center gap-2">
                    <Loader2 v-if="commentPosting" class="w-4 h-4 animate-spin" />
                    <Send v-else class="w-4 h-4" />
                  </button>
                </div>
              </form>
            </div>
            <p v-else class="text-sm text-slate-500 font-semibold mb-6">
              <router-link to="/login" class="text-primary font-bold hover:underline">Log in</router-link> to join the discussion.
            </p>

            <!-- Comments list -->
            <div v-if="commentsLoading" class="flex items-center gap-2 text-slate-400 text-sm font-bold py-4">
              <Loader2 class="w-4 h-4 animate-spin" /> Loading comments...
            </div>
            <div v-else-if="comments.length === 0" class="text-center py-8 text-slate-400 font-semibold text-sm">No comments yet. Be the first!</div>
            <ul v-else class="space-y-4">
              <li v-for="c in comments" :key="c.id" class="flex gap-3 group">
                <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm border border-slate-200 shrink-0">
                  {{ (c.user?.name || 'U')[0].toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-black text-sm text-slate-900">{{ c.user?.name || 'User' }}</span>
                    <span class="text-xs text-slate-400 font-semibold">{{ timeAgo(c.created_at) }}</span>
                    <button v-if="authStore.user?.id === c.user_id" @click="removeComment(c.id)" :disabled="deletingCommentId === c.id"
                      class="ml-auto opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-all p-1">
                      <Loader2 v-if="deletingCommentId === c.id" class="w-3.5 h-3.5 animate-spin" />
                      <Trash2 v-else class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <p class="text-sm text-slate-600 font-medium">{{ c.content }}</p>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Apply Card -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sticky top-24">
            <!-- Success state -->
            <div v-if="applySuccess || alreadyApplied" class="text-center py-4">
              <CheckCircle2 class="w-12 h-12 text-emerald-500 mx-auto mb-3" />
              <h3 class="text-lg font-black text-primary mb-1">{{ applySuccess ? 'Applied!' : 'Already Applied' }}</h3>
              <p class="text-sm text-slate-500 font-semibold">{{ applySuccess ? 'Your application has been submitted successfully.' : 'You have already applied for this position.' }}</p>
              <router-link v-if="isLoggedIn" to="/candidate/applications" class="inline-block mt-4 text-sm font-bold text-primary hover:underline">View My Applications →</router-link>
            </div>

            <!-- Apply button / form -->
            <template v-else>
              <div class="mb-4">
                <h3 class="text-lg font-black text-primary mb-1">Interested?</h3>
                <p class="text-sm text-slate-500 font-semibold">Apply now and take the next step in your career.</p>
              </div>

              <div class="flex flex-col gap-3 mb-4 text-sm">
                <div class="flex items-center gap-2 text-slate-500 font-bold"><Clock class="w-4 h-4 text-slate-400" /> Deadline: {{ formatDate(job.deadline) }}</div>
                <div v-if="job.applications_count != null" class="flex items-center gap-2 text-slate-500 font-bold"><Users class="w-4 h-4 text-slate-400" /> {{ job.applications_count }} applicant{{ job.applications_count !== 1 ? 's' : '' }}</div>
              </div>

              <!-- Not showing form yet -->
              <div v-if="!showApplyForm">
                <button v-if="isCandidate" @click="openApplyForm"
                  class="w-full bg-primary text-white py-4 rounded-2xl font-black hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-primary/20 text-sm uppercase tracking-widest cursor-pointer">
                  Apply Now
                </button>
                <router-link v-else-if="!isLoggedIn" to="/login"
                  class="block w-full bg-primary text-white py-4 rounded-2xl font-black text-center shadow-lg shadow-primary/20 text-sm uppercase tracking-widest">
                  Login to Apply
                </router-link>
                <p v-else class="text-sm text-slate-500 font-semibold text-center py-2">Only candidates can apply for jobs.</p>
              </div>

              <!-- Apply Form -->
              <form v-else @submit.prevent="submitApplication" class="space-y-4">
                <div v-if="applyError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ applyError }}</div>

                <div>
                  <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Email *</label>
                  <input v-model="applyForm.email" type="email" required :disabled="applyLoading" placeholder="your@email.com"
                    class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 disabled:opacity-50" />
                </div>

                <div>
                  <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Phone</label>
                  <input v-model="applyForm.phone" type="tel" :disabled="applyLoading" placeholder="+1 (555) 000-0000"
                    class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 disabled:opacity-50" />
                </div>

                <div>
                  <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Resume (PDF)</label>
                  <label class="flex items-center gap-3 cursor-pointer rounded-xl border-2 border-dashed border-slate-200 px-4 py-4 hover:border-primary/30 hover:bg-primary/5 transition-all">
                    <Upload class="w-5 h-5 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-500">{{ applyForm.resume ? applyForm.resume.name : 'Choose file (max 2MB)' }}</span>
                    <input type="file" accept=".pdf" @change="onFileChange" class="hidden" :disabled="applyLoading" />
                  </label>
                </div>

                <div class="flex gap-3 pt-2">
                  <button type="submit" :disabled="applyLoading"
                    class="flex-1 bg-primary text-white py-3.5 rounded-xl font-black text-sm hover:opacity-90 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                    <Loader2 v-if="applyLoading" class="w-4 h-4 animate-spin" />
                    Submit Application
                  </button>
                  <button type="button" @click="showApplyForm = false" :disabled="applyLoading"
                    class="px-4 py-3.5 rounded-xl font-bold text-sm text-slate-500 hover:text-primary transition-colors">
                    Cancel
                  </button>
                </div>
              </form>
            </template>
          </div>

          <!-- Company Info -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">About the Company</h3>
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center border border-primary/10">
                <Building2 class="w-5 h-5 text-primary" />
              </div>
              <span class="font-black text-primary">{{ job.user?.name || 'Company' }}</span>
            </div>
            <p class="text-sm text-slate-500 font-medium">Posted on {{ formatDate(job.created_at) }}</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
