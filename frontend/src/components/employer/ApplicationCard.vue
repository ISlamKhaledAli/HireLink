<script setup>
import { computed, ref } from 'vue'
import { Loader2, FileText, Download, CreditCard, ShieldCheck } from 'lucide-vue-next'
import { useApplicationsStore } from '@/stores/applications'
import api from '@/services/api'

const props = defineProps({
  /** Raw application row from GET /applications */
  application: {
    type: Object,
    required: true
  },
  readonly: {
    type: Boolean,
    default: false
  }
})

const applicationsStore = useApplicationsStore()
const loadingPayment = ref(false)

const updating = computed(() => applicationsStore.isUpdating(props.application.id))
const statusError = computed(() => applicationsStore.statusErrorFor(props.application.id))



async function handlePayment() {
  loadingPayment.value = true
  try {
    const response = await api.post(`/payments/initiate/${props.application.id}`)
    if (response.data.stripe_checkout_url) {
      window.location.href = response.data.stripe_checkout_url
    }
  } catch (error) {
    console.error('Payment initiation failed:', error)
    alert('Failed to initiate payment. Please try again.')
  } finally {
    loadingPayment.value = false
  }
}



async function handleDownload() {
  try {
    const response = await api.get(`/applications/${props.application.id}/resume`, {
      responseType: 'blob'
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `resume-${props.application.id}.pdf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Download failed:', error);
    alert('Failed to download resume.');
  }
}

function accept() {
  applicationsStore.setStatus(props.application.id, 'accepted')
}

function reject() {
  applicationsStore.setStatus(props.application.id, 'rejected')
}

function statusBadgeClass(status) {
  const map = {
    pending: 'border-amber-200 bg-amber-50 text-amber-900',
    accepted: 'border-emerald-200 bg-emerald-50 text-emerald-900',
    rejected: 'border-red-200 bg-red-50 text-red-900'
  }
  return map[status] || 'border-slate-200 bg-slate-50 text-slate-800'
}

function displayName(application) {
  const c = application.candidate
  if (c?.name) return c.name
  if (typeof c === 'object' && c?.full_name) return c.full_name
  return 'Candidate'
}
</script>

<template>
  <article
    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
    :aria-labelledby="`app-${application.id}-name`"
  >
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div class="min-w-0 flex-1">
        <h2
          :id="`app-${application.id}-name`"
          class="text-base font-black text-slate-900 truncate"
        >
          {{ displayName(application) }}
        </h2>
        <p class="mt-1 text-sm font-semibold text-slate-600 break-all">
          {{ application.email }}
        </p>
        <p v-if="application.phone" class="mt-1 text-sm font-semibold text-slate-500">
          {{ application.phone }}
        </p>
        <div v-if="application.resume_path" class="mt-4">
          <button 
            @click="handleDownload"
            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-primary border border-slate-200 rounded-xl text-xs font-black hover:bg-primary hover:text-white transition-all group"
          >
            <FileText class="w-4 h-4" />
            View Candidate CV
            <Download class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" />
          </button>
        </div>
      </div>
      <span
        class="shrink-0 inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-black capitalize"
        :class="statusBadgeClass(application.status)"
      >
        {{ application.status }}
      </span>
    </div>

    <div
      v-if="!readonly"
      class="mt-4 border-t border-slate-100 pt-4"
      :aria-busy="updating"
    >
      <p
        v-if="statusError"
        class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-800"
        role="alert"
      >
        {{ statusError }}
      </p>

      <!-- Step 1: Pending -> Accept/Reject -->
      <div v-if="application.status === 'pending'" class="flex flex-wrap items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-600 px-4 py-2 text-sm font-black text-white shadow-sm hover:bg-emerald-700 disabled:pointer-events-none disabled:opacity-50"
          :disabled="updating"
          @click="accept"
        >
          Accept Candidate
        </button>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-black text-red-700 shadow-sm hover:bg-red-50 disabled:pointer-events-none disabled:opacity-50"
          :disabled="updating"
          @click="reject"
        >
          Reject
        </button>
        <Loader2
          v-if="updating"
          class="h-5 w-5 animate-spin text-primary"
          aria-label="Updating status"
        />
      </div>

      <!-- Step 2: Accepted -> Payment -->
      <div v-else-if="application.status === 'accepted' && !application.is_paid" class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
        <p class="text-xs font-black text-amber-900 uppercase tracking-widest mb-3">Next Step: Payment Required</p>
        <p class="text-sm font-bold text-amber-800 mb-4">Please pay the hire fee to unlock candidate full profile and contact details.</p>
        <button
          @click="handlePayment"
          :disabled="loadingPayment"
          class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 rounded-xl font-black text-sm hover:scale-105 transition-all shadow-lg shadow-primary/20"
        >
          <CreditCard v-if="!loadingPayment" class="w-4 h-4" />
          <Loader2 v-else class="w-4 h-4 animate-spin" />
          Pay $10.00 to Hire
        </button>
      </div>

      <!-- Step 3: Paid & Accepted -->
      <div v-else-if="application.is_paid" class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-3 rounded-2xl border border-emerald-100">
        <ShieldCheck class="w-5 h-5" />
        <span class="text-sm font-black uppercase tracking-widest">Candidate Unlocked & Hired</span>
      </div>
    </div>
  </article>
</template>
