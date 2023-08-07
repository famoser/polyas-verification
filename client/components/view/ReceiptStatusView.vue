<script setup lang="ts">
import type { Status } from '@/components/domain/Status'
import { ReceiptErrors } from '@/components/domain/ReceiptErrors'
import ChecksView from '@/components/view/ChecksView.vue'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

defineProps<{
  result: Status
}>()

const errorOrder: ReceiptErrors[] = [ReceiptErrors.RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE, ReceiptErrors.SIGNATURE_VALID, ReceiptErrors.FINGERPRINT_REGISTERED]
</script>

<template>
  <ChecksView prefix="domain.receipt_status" :result="result" :error-order="errorOrder" :fallback-error="ReceiptErrors.UNKNOWN" @reset="emit('reset')" />
</template>
