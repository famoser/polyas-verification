<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'
import { ref } from 'vue'
import type { VerificationResult } from '@/components/domain/VerificationResult'
import VerificationResultView from '@/components/view/VerificationResultView.vue'

const { t } = useI18n()

api.addInterceptors(t)

const verificationResult = ref<VerificationResult>()
</script>

<template>
  <div class="container mw-100em">
    <div class="mt-5 mb-5">
      <HeaderJumbotron />
      <div class="mt-5">
        <UploadReceipt v-if="!verificationResult" @verification-completed="verificationResult = $event" />
        <VerificationResultView v-else :result="verificationResult" @reset="verificationResult = null" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.mw-100em {
  max-width: 40em;
}
</style>
