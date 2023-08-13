<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import type { Status } from '@/components/domain/Status'
import { useRoute } from 'vue-router'
import SetLink from '@/components/action/SetLink.vue'
import VerificationStatusView from '@/components/view/VerificationStatusView.vue'
import { api } from '@/services/api'
import SetPassword from '@/components/action/SetPassword.vue'
import { useI18n } from 'vue-i18n'

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

const password = ref<string>()

watch(password, () => {
  if (password.value && urlPayload.value) {
    doVerification()
  }
})

const verificationResult = ref<Status>()
const processingVerification = ref<boolean>()
const doVerification = async () => {
  if (!urlPayload.value || !password.value) {
    return
  }

  const payload = { ...urlPayload.value, password: password.value }
  processingVerification.value = true
  verificationResult.value = await api.postVerification(payload)
  processingVerification.value = false
}

const reset = () => {
  password.value = undefined
  verificationResult.value = undefined
}

const { t } = useI18n()
</script>

<template>
  <SetLink v-if="!urlPayload" />
  <SetPassword v-else-if="!password" @changed="password = $event" />
  <p v-else-if="processingVerification">
    {{ t('view.verify_app.processing') }}
  </p>
  <VerificationStatusView v-if="verificationResult" :result="verificationResult" @reset="reset" />
</template>
