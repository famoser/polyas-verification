<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import type { Status } from '@/components/domain/Status'
import { useRoute, useRouter } from 'vue-router'
import SetLink from '@/components/action/SetLink.vue'
import { api } from '@/services/api'
import SetPassword from '@/components/action/SetPassword.vue'
import { VerificationErrors } from '@/components/domain/VerificationErrors'
import ChecksView from '@/components/view/library/ChecksView.vue'
import VerificationExplanation from '@/components/layout/VerificationExplanation.vue'
import ResetButton from '@/components/shared/ResetButton.vue'
import { VerificationSteps } from '@/components/domain/VerificationSteps'
import StepView from '@/components/view/library/StepView.vue'
import VerifyBallotContent from '@/components/action/VerifyBallotContent.vue'
import DownloadReceipt from '@/components/action/DownloadReceipt.vue'
import { useTranslator } from '@/locales/translator'

const route = useRoute()
const decodeUrlBase64 = (value: string) => {
  const base64 = value.replace(/-/g, '+').replace(/_/g, '/')
  return base64 + '='.repeat((4 - (base64.length % 4)) % 4)
}

const urlPayload = computed(() => {
  const c = route.query?.c
  const d = route.query?.d
  const vid = route.query?.vid
  const nonce = route.query?.nonce
  if (!c || Array.isArray(c) || !d || Array.isArray(d) || !vid || Array.isArray(vid) || !nonce || Array.isArray(nonce)) {
    return null
  }

  return { c, d, vid, nonce }
})

const router = useRouter()
const backVerify = computed(() => {
  router.currentRoute.value // need this for reactivity
  return router.options.history.state.back && router.options.history.state.back.toString().startsWith('/verify')
})

const reset = () => {
  password.value = undefined
  verificationResult.value = undefined
  ballotContentVerifiedResult.value = undefined
  receiptDownloaded.value = undefined
  if (backVerify.value) {
    router.back()
  }
}

const canReset = computed(() => {
  return password.value || backVerify.value
})

const password = ref<string>()

watch(password, () => {
  if (password.value && urlPayload.value) {
    doVerification()
  }
})

const verificationResult = ref<Status>()
const doVerification = async () => {
  if (!urlPayload.value || !password.value) {
    return
  }

  const payload = {
    ...urlPayload.value,
    c: decodeUrlBase64(urlPayload.value.c),
    d: decodeUrlBase64(urlPayload.value.d),
    password: password.value
  }
  verificationResult.value = await api.postVerification(payload)
}

const errorOrder: VerificationErrors[] = [
  VerificationErrors.LOGIN_SUCCESSFUL,
  VerificationErrors.DEVICE_PARAMETERS_MATCH,
  VerificationErrors.SIGNATURE_VALID,
  VerificationErrors.ASSOCIATION_VALID,
  VerificationErrors.RECEIPT_STORED,
  VerificationErrors.QR_CODE_DECRYPTION,
  VerificationErrors.CHALLENGE_SUCCESSFUL,
  VerificationErrors.ZKP_VALID,
  VerificationErrors.BALLOT_DECODE
]

const ballotContentVerifiedResult = ref<boolean>()
const receiptDownloaded = ref<boolean>()

const { t } = useTranslator()
</script>

<template>
  <div class="mb-4">
    <h3 class="mb-2">{{ t('view.verify_app.title') }}</h3>
    <p>{{ t('view.verify_app.description') }}</p>
  </div>

  <div class="row g-2">
    <div class="p-0" v-if="canReset">
      <ResetButton @reset="reset" />
    </div>

    <StepView prefix="domain.verification_step" :entry="VerificationSteps.INITIALIZE" :done="!!urlPayload" :success="true" :force-closed-when-done="true">
      <SetLink />
    </StepView>

    <StepView v-if="urlPayload" prefix="domain.verification_step" :entry="VerificationSteps.ENTER_PASSWORD" :done="!!password" :success="true" :force-closed-when-done="true">
      <SetPassword @changed="password = $event" :voterId="urlPayload.vid" />
    </StepView>

    <StepView v-if="urlPayload && password" prefix="domain.verification_step" :entry="VerificationSteps.RECOVER_BALLOT" :done="!!verificationResult" :success="!!verificationResult?.status">
      <div class="row g-2">
        <ChecksView prefix="domain.verification_status" :result="verificationResult" :error-order="errorOrder" :fallback-error="VerificationErrors.UNKNOWN" />
      </div>
    </StepView>

    <StepView
      v-if="!!(verificationResult?.result)"
      prefix="domain.verification_step"
      :entry="VerificationSteps.VERIFY_BALLOT_CONTENT"
      :done="ballotContentVerifiedResult !== undefined"
      :success="!!ballotContentVerifiedResult"
    >
      <VerifyBallotContent :choice="verificationResult.result" @verified="ballotContentVerifiedResult = $event" :decision="ballotContentVerifiedResult" />
    </StepView>

    <StepView
      v-if="!!(ballotContentVerifiedResult && verificationResult?.receipt)"
      prefix="domain.verification_step"
      :entry="VerificationSteps.STORE_RECEIPT"
      :done="receiptDownloaded !== undefined"
      :success="receiptDownloaded ? true : undefined"
    >
      <DownloadReceipt :receipt="verificationResult.receipt" @downloaded="receiptDownloaded = $event" />
    </StepView>
  </div>

  <p class="alert alert-info my-5" v-if="receiptDownloaded !== undefined">
    {{ t('view.verify_app.verification_finished') }}
  </p>

  <div class="my-5">
    <VerificationExplanation />
  </div>
</template>
