<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'
import { ref } from 'vue'
import type { VerificationResult } from '@/components/domain/VerificationResult'
import VerificationResultView from '@/components/view/VerificationResultView.vue'
import VerificationExplanation from '@/components/layout/FAQ.vue'
import ElectionView from '@/components/view/ElectionView.vue'
import type { Election } from '@/components/domain/Election'

const { t } = useI18n()

api.addInterceptors(t)

const verificationResult = ref<VerificationResult>()
const election = ref<Election>()

api.getElection().then((result) => (election.value = result))
</script>

<template>
  <div class="container mw-100em">
    <div class="my-5">
      <HeaderJumbotron />
      <ElectionView class="my-5" v-if="election" :election="election" />
      <UploadReceipt v-if="!verificationResult" @verification-completed="verificationResult = $event" />
      <VerificationResultView v-else :result="verificationResult" @reset="verificationResult = undefined" />
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
