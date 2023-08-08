<script setup lang="ts">
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import { ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import ReceiptExplanation from '@/components/layout/ReceiptExplanation.vue'
import ReceiptStatusView from '@/components/view/ReceiptStatusView.vue'

const receiptStatus = ref<Status>()
</script>

<template>
  <UploadReceipt v-if="!receiptStatus" @verification-completed="receiptStatus = $event" />
  <ReceiptStatusView v-if="receiptStatus" :result="receiptStatus" @reset="receiptStatus = undefined" />
  <div class="my-5">&nbsp;</div>
  <ReceiptExplanation />
</template>
