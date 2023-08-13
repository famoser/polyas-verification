<script setup lang="ts">
import type { Status } from '@/components/domain/Status'
import ChecksView from '@/components/view/ChecksView.vue'
import { VerificationErrors } from '@/components/domain/VerificationErrors'
import BallotsView from '@/components/view/BallotsView.vue'
import VerificationExplanation from '@/components/layout/VerificationExplanation.vue'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

defineProps<{
  result: Status
}>()

const errorOrder: VerificationErrors[] = [
  VerificationErrors.LOGIN_SUCCESSFUL,
  VerificationErrors.DEVICE_PARAMETERS_MATCH,
  VerificationErrors.SIGNATURE_VALID,
  VerificationErrors.QR_CODE_DECRYPTION,
  VerificationErrors.CHALLENGE_SUCCESSFUL,
  VerificationErrors.ZKP_VALID,
  VerificationErrors.BALLOT_DECODE
]
</script>

<template>
  <BallotsView v-if="result.status && result.result" :choice="result.result" />
  <p class="mt-5 mb-2">Erfolgte Checks</p>
  <ChecksView prefix="domain.verification_status" :result="result" :error-order="errorOrder" :fallback-error="VerificationErrors.UNKNOWN" @reset="emit('reset')" />
  <div class="my-5">&nbsp;</div>
  <VerificationExplanation />
</template>
