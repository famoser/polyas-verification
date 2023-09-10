<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import type { Status } from '@/components/domain/Status'
import { useRoute, useRouter } from 'vue-router'
import SetLink from '@/components/action/SetLink.vue'
import { api } from '@/services/api'
import SetPassword from '@/components/action/SetPassword.vue'
import { useI18n } from 'vue-i18n'
import CheckView from '@/components/view/CheckView.vue'
import { VerificationErrors } from '@/components/domain/VerificationErrors'
import ChecksView from '@/components/view/ChecksView.vue'
import VerificationExplanation from '@/components/layout/VerificationExplanation.vue'
import BallotsView from '@/components/view/BallotsView.vue'
import ResetButton from '@/components/shared/ResetButton.vue'

const route = useRoute()
const urlPayload = computed(() => {
  const payload = route.query?.c
  const voterId = route.query?.vid
  const nonce = route.query?.nonce
  if (!payload || Array.isArray(payload) || !voterId || Array.isArray(voterId) || !nonce || Array.isArray(nonce)) {
    return null
  }

  return { payload, voterId, nonce }
})

const router = useRouter()
const reset = () => {
  password.value = undefined
  verificationResult.value = undefined
  checksShown.value = undefined
  if (router.options.history.state.back) {
    router.back()
  }
}

const canReset = computed(() => {
  return password.value || router.options.history.state.back
})

const password = ref<string>()

watch(password, () => {
  if (password.value && urlPayload.value) {
    doVerification()
  }
})

const verificationResult = ref<Status>()
const checksShown = ref<boolean>()
const doVerification = async () => {
  if (!urlPayload.value || !password.value) {
    return
  }

  const payload = { ...urlPayload.value, password: password.value }
  verificationResult.value = await api.postVerification(payload)
}

const errorOrder: VerificationErrors[] = [
  VerificationErrors.LOGIN_SUCCESSFUL,
  VerificationErrors.DEVICE_PARAMETERS_MATCH,
  VerificationErrors.SIGNATURE_VALID,
  VerificationErrors.QR_CODE_DECRYPTION,
  VerificationErrors.CHALLENGE_SUCCESSFUL,
  VerificationErrors.ZKP_VALID,
  VerificationErrors.BALLOT_DECODE
]

const { t } = useI18n()
</script>

<template>
  <div class="mb-4">
    <h3 class="mb-2">{{ t('view.verify_app.title') }}</h3>
    <p>{{ t('view.verify_app.description') }}</p>
  </div>

  <div class="row g-2">
    <SetLink v-if="!urlPayload" />
    <div class="p-0" v-if="canReset">
      <ResetButton @reset="reset" />
    </div>
    <CheckView v-if="urlPayload" prefix="domain.verification_status" :entry="VerificationErrors.LINK_ENTERED" :success="true" />

    <SetPassword class="mt-4" v-if="urlPayload && !password" @changed="password = $event" />
    <CheckView v-if="urlPayload && password" prefix="domain.verification_status" :entry="VerificationErrors.PASSWORD_ENTERED" :success="true" />

    <ChecksView
      v-if="urlPayload && password"
      prefix="domain.verification_status"
      :result="verificationResult"
      :error-order="errorOrder"
      :fallback-error="VerificationErrors.UNKNOWN"
      @checks-finished-loading="checksShown = true"
    />
  </div>

  <div class="my-5" v-if="checksShown && verificationResult && verificationResult.status && verificationResult.result">
    <BallotsView :choice="verificationResult.result" />
  </div>

  <div class="my-5">
    <VerificationExplanation />
  </div>
</template>
