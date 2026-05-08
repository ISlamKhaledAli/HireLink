<script setup>
import { Check, X, Shield, Zap, Award, Star } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()

const plans = [
  {
    name: 'Free Starter',
    price: '$0',
    description: 'Perfect for small teams and startups testing the waters.',
    features: [
      'Post 1 job listing',
      'Basic candidate search',
      'Email notifications',
      'Standard support',
    ],
    notIncluded: [
      'Featured placement',
      'Direct candidate messaging',
      'Advanced analytics',
      'API access'
    ],
    buttonText: 'Get Started',
    popular: false,
    color: 'bg-white text-slate-900 border-slate-200'
  },
  {
    name: 'Standard Pro',
    price: '$49',
    period: '/month',
    description: 'Ideal for growing companies with consistent hiring needs.',
    features: [
      'Post 10 job listings',
      'Priority candidate matching',
      'Direct messaging',
      'Advanced filters',
      'Company branding',
      'Featured for 7 days'
    ],
    notIncluded: [
      'Multi-user access',
      'API integration'
    ],
    buttonText: 'Try Pro Now',
    popular: true,
    color: 'bg-primary text-white border-primary shadow-xl shadow-primary/20'
  },
  {
    name: 'Enterprise',
    price: '$199',
    period: '/month',
    description: 'Full-scale solution for large organizations and recruiters.',
    features: [
      'Unlimited job listings',
      'Dedicated account manager',
      'Full API access',
      'Custom workflows',
      'Team collaboration',
      'Unlimited Featured listings',
      'White-label options'
    ],
    notIncluded: [],
    buttonText: 'Contact Sales',
    popular: false,
    color: 'bg-slate-900 text-white border-slate-800 shadow-xl'
  }
]
</script>

<template>
  <div class="bg-[#FAFAFC] min-h-screen py-20 px-6 font-display">
    <div class="max-w-7xl mx-auto text-center mb-20">
      <div class="inline-flex items-center gap-2 px-4 py-2 bg-secondary/10 text-secondary rounded-full text-xs font-black uppercase tracking-widest mb-6">
        <Star class="w-4 h-4 fill-secondary" />
        Pricing Plans
      </div>
      <h1 class="text-5xl md:text-6xl font-black text-primary tracking-tight mb-6">
        Invest in your <span class="text-secondary">future team</span>
      </h1>
      <p class="text-slate-500 font-bold text-lg max-w-2xl mx-auto">
        Choose the plan that fits your hiring velocity. From solo founders to global enterprises, we've got you covered.
      </p>
    </div>

    <!-- Pricing Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto items-start">
      <div 
        v-for="plan in plans" 
        :key="plan.name"
        class="relative p-8 rounded-3xl border-2 transition-all duration-500 hover:-translate-y-2 group"
        :class="plan.color"
      >
        <!-- Popular Badge -->
        <div v-if="plan.popular" class="absolute -top-4 left-1/2 -translate-x-1/2 bg-secondary text-white px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest shadow-lg">
          Most Popular
        </div>

        <div class="mb-8">
          <h3 class="text-xl font-black mb-2 uppercase tracking-tight">{{ plan.name }}</h3>
          <div class="flex items-baseline gap-1 mb-4">
            <span class="text-5xl font-black tracking-tighter">{{ plan.price }}</span>
            <span v-if="plan.period" class="text-sm font-bold opacity-70">{{ plan.period }}</span>
          </div>
          <p class="text-sm font-semibold opacity-80 leading-relaxed">{{ plan.description }}</p>
        </div>

        <div class="space-y-4 mb-10">
          <div v-for="feature in plan.features" :key="feature" class="flex items-center gap-3">
            <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
              <Check class="w-3 h-3 text-emerald-500" />
            </div>
            <span class="text-sm font-bold opacity-90">{{ feature }}</span>
          </div>
          
          <div v-for="feature in plan.notIncluded" :key="feature" class="flex items-center gap-3 opacity-40">
            <div class="w-5 h-5 rounded-full bg-slate-300 flex items-center justify-center shrink-0">
              <X class="w-3 h-3 text-slate-500" />
            </div>
            <span class="text-sm font-bold">{{ feature }}</span>
          </div>
        </div>

        <button 
          @click="router.push('/register')"
          class="w-full py-4 rounded-2xl font-black transition-all shadow-lg flex items-center justify-center gap-2"
          :class="plan.popular ? 'bg-white text-primary hover:bg-slate-50' : (plan.name === 'Enterprise' ? 'bg-primary text-white hover:bg-slate-800' : 'bg-primary text-white hover:bg-slate-800')"
        >
          {{ plan.buttonText }}
          <Zap v-if="plan.popular" class="w-4 h-4 fill-primary" />
        </button>
      </div>
    </div>

    <!-- Trust Banner -->
    <div class="mt-32 max-w-5xl mx-auto">
      <div class="bg-white rounded-3xl p-10 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center gap-10">
        <div class="flex gap-8 flex-1 items-center justify-around opacity-40">
           <div class="font-black text-2xl text-slate-400">MICROSOFT</div>
           <div class="font-black text-2xl text-slate-400">GOOGLE</div>
           <div class="font-black text-2xl text-slate-400">AIRBNB</div>
        </div>
        <div class="md:border-l border-slate-100 md:pl-10 text-center md:text-left">
          <p class="text-sm font-bold text-slate-500 mb-2">Trusted by 2,000+ companies worldwide</p>
          <div class="flex items-center justify-center md:justify-start gap-1">
            <Star v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400 fill-yellow-400" />
            <span class="text-primary font-black ml-2">4.9/5 Rating</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
