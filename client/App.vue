<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'
import { ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import ReceiptExplanation from '@/components/layout/ReceiptExplanation.vue'
import ElectionView from '@/components/view/ElectionView.vue'
import type { Election } from '@/components/domain/Election'
import ReceiptStatusView from '@/components/view/ReceiptStatusView.vue'
import type { ElectionDetails } from '@/components/domain/ElectionDetails'
import VerifyBallot from '@/components/action/VerifyBallot.vue'

const { t } = useI18n()

api.addInterceptors(t)

const receiptStatus = ref<Status>()
const verificationStatus = ref<Status>()
const election = ref<Election>()
const electionDetails = ref<ElectionDetails>()

api.getElection().then((result) => (election.value = result))
api.getElectionDetails().then((result) => (electionDetails.value = result))
</script>

<template>
  <div class="container mw-100em">
    <div class="my-5">
      <HeaderJumbotron />
      <ElectionView class="my-5" v-if="election && electionDetails" :election="election" :election-details="electionDetails" />
      <VerifyBallot @verification-completed="verificationStatus = $event" />
      <p class="mt-3 mb-3">oder TODO trans</p>
      <UploadReceipt v-if="!receiptStatus" @verification-completed="receiptStatus = $event" />
      <template v-if="receiptStatus">
        <ReceiptStatusView :result="receiptStatus" @reset="receiptStatus = undefined" />
        <div class="my-5">&nbsp;</div>
        <ReceiptExplanation />
      </template>
    </div>
  </div>
</template>

<style scoped>
.mw-100em {
  max-width: 35em;
}
</style>
