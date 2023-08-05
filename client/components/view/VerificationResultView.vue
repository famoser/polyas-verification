<script setup lang="ts">
import type { Status } from '@/components/domain/Status'
import { useI18n } from 'vue-i18n'
import { ReceiptErrors } from '@/components/domain/ReceiptErrors'
import CheckView from '@/components/view/CheckView.vue'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

const props = defineProps<{
  result: Status
}>()

const entries: ReceiptErrors[] = [ReceiptErrors.RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE, ReceiptErrors.SIGNATURE_VALID, ReceiptErrors.FINGERPRINT_REGISTERED]
const errorKnown = props.result.error && entries.includes(props.result.error as ReceiptErrors)
const errorEntryIndex = entries.indexOf(props.result.error as ReceiptErrors)

const { t } = useI18n()
</script>

<template>
  <div class="row g-2">
    <div class="col-12" v-for="entry in entries" :key="entry">
      <CheckView :entry="entry" :success="result[entry]" />
    </div>
    <div class="col-12">
      <button class="btn btn-outline-primary" @click="emit('reset')">
        {{ t('view.verification_result_view.reset') }}
      </button>
    </div>
  </div>
</template>
