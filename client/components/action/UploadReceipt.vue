<script setup lang="ts">
import { api } from '@/services/api'
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import { ref } from 'vue'
import type { Status } from '@/components/domain/Status'

const emit = defineEmits<{
  (e: 'verificationCompleted', result: Status): void
}>()

const processingReceipt = ref(false)

const uploadReceipt = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.item(0)
  if (!file) {
    return
  }

  processingReceipt.value = true
  const verificationResult = await api.postReceipt(file)
  emit('verificationCompleted', verificationResult)
  processingReceipt.value = false
}

const uniqueId = String(Math.random())

const { t } = useI18n()
</script>

<template>
  <label class="btn btn-lg btn-primary" :class="{ disabled: processingReceipt }" :for="uniqueId">
    <template v-if="!processingReceipt">{{ t('action.upload_receipt.title') }}</template>
    <template v-else>{{ t('action.upload_receipt.processing') }}</template>
  </label>
  <input type="file" accept="application/pdf" @change="uploadReceipt" :id="uniqueId" class="d-none" />
  <div class="form-text">
    {{ t('action.upload_receipt.privacy_is_safeguarded') }}
    <InfoPopover :message="t('action.upload_receipt.privacy_is_safeguarded_help')" />
  </div>
</template>
