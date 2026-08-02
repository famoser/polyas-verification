<script setup lang="ts">
import ExpandedIndicator from '@/components/shared/ExpandedIndicator.vue'
import { ref } from 'vue'

defineProps<{
  forceOpen?: boolean
  question: string
}>()

const showBody = ref(false)
</script>

<template>
  <div>
    <div class="d-flex flex-row" role="button" @click="showBody = !showBody">
      <p>
        <b>{{ question }}</b>
      </p>
      <div class="ms-auto">
        <ExpandedIndicator :expanded="showBody || !!forceOpen" class="ms-auto" />
      </div>
    </div>
    <div v-if="showBody || forceOpen" class="mb-5">
      <slot></slot>
    </div>
  </div>
</template>
