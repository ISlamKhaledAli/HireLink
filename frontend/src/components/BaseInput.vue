<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: [Object, Function, null],
    default: null
  },
  rounded: {
    type: String,
    default: 'rounded-xl'
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'enter'])

// Use inline style for padding-left to bypass any Tailwind compilation or specificity issues
const inputStyle = computed(() => ({
  paddingLeft: props.icon ? '56px' : '16px',
  paddingRight: '16px',
}))
</script>

<template>
  <div class="base-input-wrapper" :class="[rounded]">
    <!-- Icon: absolutely positioned, vertically centered -->
    <span v-if="icon" class="base-input-icon">
      <component :is="icon" />
    </span>

    <!-- Input: padding is enforced via inline style, not Tailwind class -->
    <input
      :type="type"
      :value="modelValue"
      :disabled="disabled"
      @input="emit('update:modelValue', $event.target.value)"
      @keyup.enter="emit('enter')"
      :placeholder="placeholder"
      :style="inputStyle"
      :class="[rounded]"
      class="base-input-field"
    />
  </div>
</template>

<style scoped>
.base-input-wrapper {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
}

.base-input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  color: #94a3b8; /* slate-400 */
  transition: color 0.2s ease;
}

.base-input-wrapper:focus-within .base-input-icon {
  color: #3b82f6; /* blue-500 */
}

.base-input-field {
  width: 100%;
  padding-top: 12px;
  padding-bottom: 12px;
  background-color: #f8fafc; /* slate-50 */
  border: 1px solid #e2e8f0; /* slate-200 */
  font-size: 14px;
  font-weight: 600;
  color: #0f172a; /* slate-900 */
  outline: none;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.base-input-field::placeholder {
  color: #94a3b8; /* slate-400 */
  font-weight: 500;
}

.base-input-field:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
