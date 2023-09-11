<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'

const emit = defineEmits<{
  (e: 'uploaded', file: File): void
}>()

const uploadReceipt = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.item(0)
  if (!file) {
    return
  }

  emit('uploaded', file)
}

const uniqueId = String(Math.random())

const { t } = useI18n()
</script>

<template>
  <div>
    <label class="btn btn-lg btn-primary" :for="uniqueId">
      {{ t('action.upload_receipt.title') }}
    </label>
    <input type="file" accept="application/pdf" @change="uploadReceipt" :id="uniqueId" class="d-none" />

    <InfoPopover :message="t('action.upload_receipt.privacy_is_safeguarded')" :popover="t('action.upload_receipt.privacy_is_safeguarded_help')" />
  </div>
</template>
