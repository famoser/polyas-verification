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

const errorOrder: ReceiptErrors[] = [ReceiptErrors.RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE, ReceiptErrors.SIGNATURE_VALID, ReceiptErrors.FINGERPRINT_REGISTERED]
const errorKnown = !!props.result.error && errorOrder.includes(props.result.error as ReceiptErrors)
const errorUnknown = !props.result.status && !errorOrder.includes(props.result.error as ReceiptErrors)
const errorEntryIndex = errorOrder.indexOf(props.result.error as ReceiptErrors)

const successPerEntry: { [K in ReceiptErrors]?: boolean | undefined } = {}
errorOrder.forEach((knownEntry, index) => {
  if (!props.result.error || (errorKnown && errorEntryIndex < index)) {
    successPerEntry[knownEntry] = true
  }

  if (!errorKnown || errorEntryIndex < index) {
    successPerEntry[knownEntry] = undefined
  }

  if (errorEntryIndex === index) {
    successPerEntry[knownEntry] = false
  }
})

const { t } = useI18n()
</script>

<template>
  <div class="row g-2">
    <CheckView v-if="errorUnknown" :entry="ReceiptErrors.UNKNOWN" :success="false" prefix="domain.receipt_status" />
    <div class="col-12" v-for="entry in errorOrder" :key="entry">
      <CheckView :entry="entry" :success="successPerEntry[entry]" prefix="domain.receipt_status" />
    </div>
    <div class="col-12">
      <button class="btn btn-outline-primary" @click="emit('reset')">
        {{ t('view.verification_result_view.reset') }}
      </button>
    </div>
  </div>
</template>
