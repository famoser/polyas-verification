<script setup lang="ts">
import type { Status } from '@/components/domain/Status'
import ChecksView from '@/components/view/ChecksView.vue'
import { VerificationErrors } from '@/components/domain/VerificationErrors'

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
  <ChecksView prefix="domain.verification_status" :result="result" :error-order="errorOrder" :fallback-error="VerificationErrors.UNKNOWN" @reset="emit('reset')" />
</template>
