<script setup lang="ts">
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import { computed, ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import ReceiptExplanation from '@/components/layout/ReceiptExplanation.vue'
import { ReceiptErrors } from '@/components/domain/ReceiptErrors'
import { api } from '@/services/api'
import ChecksView from '@/components/view/library/ChecksView.vue'
import { useI18n } from 'vue-i18n'
import ResetButton from '@/components/shared/ResetButton.vue'
import TextCheckView from '@/components/view/library/TextCheckView.vue'

const receiptStatus = ref<Status>()
const fileSet = ref<boolean>()
const checksShown = ref<boolean>()
const doVerification = async (file: File) => {
  fileSet.value = true
  receiptStatus.value = await api.postReceipt(file)
}

const reset = () => {
  fileSet.value = false
  checksShown.value = false
  receiptStatus.value = undefined
}

const canReset = computed(() => {
  return fileSet.value
})

const errorOrder: ReceiptErrors[] = [ReceiptErrors.RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE, ReceiptErrors.SIGNATURE_VALID]

const { t } = useI18n()
</script>

<template>
  <div class="mb-4">
    <h3 class="mb-2">{{ t('view.receipt_app.title') }}</h3>
    <p>{{ t('view.receipt_app.description') }}</p>
  </div>

  <div class="row g-2">
    <div class="p-0" v-if="canReset">
      <ResetButton @reset="reset" />
    </div>
    <UploadReceipt v-if="!fileSet" @uploaded="doVerification($event)" />
    <TextCheckView v-if="fileSet" prefix="domain.receipt_status" :entry="ReceiptErrors.RECEIPT_UPLOADED" :success="true" />

    <ChecksView v-if="fileSet" prefix="domain.receipt_status" :result="receiptStatus" :error-order="errorOrder" :fallback-error="ReceiptErrors.UNKNOWN" @checks-finished-loading="checksShown = true" />
  </div>

  <p class="my-5 alert alert-success" v-if="checksShown && receiptStatus && receiptStatus.status">
    {{ t('view.receipt_app.receipt_valid') }}
  </p>

  <div class="my-5">
    <ReceiptExplanation />
  </div>
</template>
