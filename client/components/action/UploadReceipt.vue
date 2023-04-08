<script setup lang="ts">
import { api } from '@/services/api'
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'

const emit = defineEmits<{
  (e: 'uploadStarted'): void
  (e: 'uploadFinished'): void
}>()

const uploadReceipt = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.item(0)
  if (!file) {
    return
  }

  emit('uploadStarted')
  await api.postReceipt(file)
  emit('uploadFinished')
}

const uniqueId = String(Math.random())

const { t } = useI18n()
</script>

<template>
  <label class="btn btn-lg btn-primary" :for="uniqueId">{{ t('action.upload_receipt.title') }}</label>
  <input type="file" accept="application/pdf" @change="uploadReceipt" :id="uniqueId" class="d-none" />
  <div class="form-text">
    {{ t('action.upload_receipt.privacy_is_safeguarded') }}
    <InfoPopover :message="t('action.upload_receipt.privacy_is_safeguarded_help')" />
  </div>
</template>
