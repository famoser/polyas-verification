<script setup lang="ts">
import { api } from '@/services/api'
import { useI18n } from 'vue-i18n'
import { computed, ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import InfoPopover from '@/components/shared/InfoPopover.vue'

const emit = defineEmits<{
  (e: 'verificationCompleted', result: Status): void
}>()

const processingVerification = ref(false)
const link = ref<string>()
const reset = () => {
  link.value = undefined
}
const urlPayload = computed(() => {
  if (!link.value) {
    return null
  }

  const url = new URL(link.value)
  const payload = url.searchParams.get('c')
  const voterId = url.searchParams.get('vid')
  const nonce = url.searchParams.get('nonce')
  if (!payload || !voterId || !nonce) {
    return null
  }

  return { payload, voterId, nonce }
})

const password = ref<string>()
const passwordValid = computed(() => password.value && password.value.length === 6)

const doVerification = async () => {
  if (!urlPayload.value || !password.value) {
    return
  }

  const payload = { ...urlPayload.value, password: password.value }
  processingVerification.value = true
  const verificationResult = await api.postVerification(payload)
  emit('verificationCompleted', verificationResult)
  processingVerification.value = false
}

const { t } = useI18n()
</script>

<template>
  <div>
    <template v-if="!urlPayload">
      <input class="form-control" v-model="link" :placeholder="t('action.verify_ballot.paste_link')" />
      <div class="form-text">
        {{ t('action.verify_ballot.full_verification') }}
        <InfoPopover :message="t('action.verify_ballot.full_verification_help')" />
      </div>
      <p v-if="link" class="alert alert-danger">
        {{ t('action.verify_ballot.link_invalid') }}
        <span role="button" @click="reset">{{ t('action.verify_ballot.reset') }}</span>
      </p>
    </template>
    <template v-else>
      <input v-model="password" />
    </template>
  </div>
</template>
