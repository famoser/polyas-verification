<script setup lang="ts">
import type { VerificationResult } from '@/components/domain/VerificationResult'
import VerificationEntryView from '@/components/view/VerificationEntryView.vue'
import { useI18n } from 'vue-i18n'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

defineProps<{
  result: VerificationResult
}>()

const entries: (keyof VerificationResult)[] = ['RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE', 'SIGNATURE_VALID', 'FINGERPRINT_REGISTERED']

const { t } = useI18n()
</script>

<template>
  <div class="row g-2">
    <div class="col-12" v-for="entry in entries" :key="entry">
      <VerificationEntryView :entry="entry" :success="result[entry]" />
    </div>
    <div class="col-12">
      <button class="btn btn-outline-primary" @click="emit('reset')">
        {{ t('view.verification_result_view.reset') }}
      </button>
    </div>
  </div>
</template>
