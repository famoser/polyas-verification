<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'
import { ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import VerificationExplanation from '@/components/layout/FAQ.vue'
import ElectionView from '@/components/view/ElectionView.vue'
import type { Election } from '@/components/domain/Election'
import ReceiptStatusView from '@/components/view/ReceiptStatusView.vue'

const { t } = useI18n()

api.addInterceptors(t)

const receiptStatus = ref<Status>()
const election = ref<Election>()

api.getElection().then((result) => (election.value = result))
</script>

<template>
  <div class="container mw-100em">
    <div class="my-5">
      <HeaderJumbotron />
      <ElectionView class="my-5" v-if="election" :election="election" />
      <UploadReceipt v-if="!receiptStatus" @verification-completed="receiptStatus = $event" />
      <ReceiptStatusView v-else :result="receiptStatus" @reset="receiptStatus = undefined" />
      <div class="my-5">&nbsp;</div>
      <VerificationExplanation />
    </div>
  </div>
</template>

<style scoped>
.mw-100em {
  max-width: 35em;
}
</style>
