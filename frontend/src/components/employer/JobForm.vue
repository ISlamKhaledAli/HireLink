<script setup>
import BaseInput from '@/components/BaseInput.vue'
import { Briefcase, MapPin, DollarSign } from 'lucide-vue-next'

defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  disabled: {
    type: Boolean,
    default: false
  },
  categoriesLoading: {
    type: Boolean,
    default: false
  },
  minDeadline: {
    type: String,
    required: true
  }
})

const form = defineModel('form', { required: true })

const emit = defineEmits(['submit'])

function fieldClass(err) {
  return err ? 'ring-2 ring-red-200 border-red-300' : ''
}
</script>

<template>
  <form class="flex flex-col gap-8" @submit.prevent="emit('submit')">
    <div>
      <label
        for="job-category"
        class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block"
      >
        Category
      </label>
      <select
        id="job-category"
        v-model="form.category_id"
        :disabled="disabled || categoriesLoading"
        required
        class="w-full rounded-xl bg-slate-50 border px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 disabled:opacity-60"
        :class="fieldClass(errors.category_id)"
      >
        <option disabled value="">Select a category</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">
          {{ c.name }}
        </option>
      </select>
      <p v-if="errors.category_id" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.category_id }}
      </p>
    </div>

    <div>
      <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">
        Job title
      </label>
      <BaseInput
        v-model="form.title"
        :icon="Briefcase"
        placeholder="e.g. Senior Frontend Engineer"
        :disabled="disabled"
      />
      <p v-if="errors.title" class="mt-1.5 text-xs font-semibold text-red-600">{{ errors.title }}</p>
    </div>

    <div>
      <label
        for="job-description"
        class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block"
      >
        Description
      </label>
      <textarea
        id="job-description"
        v-model="form.description"
        rows="6"
        required
        :disabled="disabled"
        placeholder="Describe the role, responsibilities, and requirements."
        class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 placeholder:font-medium focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 resize-y min-h-[140px] disabled:opacity-60"
        :class="fieldClass(errors.description)"
      />
      <p v-if="errors.description" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.description }}
      </p>
    </div>

    <div>
      <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">
        Location
      </label>
      <BaseInput
        v-model="form.location"
        :icon="MapPin"
        placeholder="City, region, or Remote"
        :disabled="disabled"
      />
      <p v-if="errors.location" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.location }}
      </p>
    </div>

    <div>
      <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">
        Work type
      </label>
      <div class="flex flex-wrap gap-3">
        <label
          v-for="wt in ['remote', 'onsite', 'hybrid']"
          :key="wt"
          class="flex items-center gap-2 cursor-pointer rounded-xl border-2 px-4 py-2.5 text-sm font-bold capitalize transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary border-slate-200 bg-white text-slate-600"
        >
          <input
            v-model="form.work_type"
            type="radio"
            name="work_type"
            :value="wt"
            :disabled="disabled"
            class="sr-only"
          />
          {{ wt }}
        </label>
      </div>
      <p v-if="errors.work_type" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.work_type }}
      </p>
    </div>

    <div>
      <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">
        Salary range <span class="font-bold normal-case text-slate-400">(optional)</span>
      </label>
      <BaseInput
        v-model="form.salary_range"
        :icon="DollarSign"
        placeholder="e.g. $80k – $110k"
        :disabled="disabled"
      />
      <p v-if="errors.salary_range" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.salary_range }}
      </p>
    </div>

    <div>
      <label
        for="job-deadline"
        class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block"
      >
        Application deadline
      </label>
      <input
        id="job-deadline"
        v-model="form.deadline"
        type="date"
        required
        :min="minDeadline"
        :disabled="disabled"
        class="w-full max-w-xs rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-3 focus:ring-primary/15 disabled:opacity-60"
        :class="fieldClass(errors.deadline)"
      />
      <p class="mt-2 text-xs font-medium text-slate-500">
        Must be after today (server validates date).
      </p>
      <p v-if="errors.deadline" class="mt-1.5 text-xs font-semibold text-red-600">
        {{ errors.deadline }}
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-4 pt-2">
      <button
        type="submit"
        :disabled="disabled || categoriesLoading || !categories.length"
        class="bg-primary text-white px-8 py-3.5 rounded-xl font-black text-sm shadow-lg shadow-primary/20 hover:opacity-95 active:scale-[0.98] transition-all disabled:opacity-50 disabled:pointer-events-none"
      >
        Publish listing
      </button>
      <router-link
        :to="{ name: 'employer-jobs' }"
        class="text-sm font-bold text-slate-500 hover:text-primary transition-colors"
      >
        Cancel
      </router-link>
    </div>
  </form>
</template>
