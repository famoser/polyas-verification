<script setup lang="ts">
import InfoPopover from '@/components/shared/InfoPopover.vue'
import type { Receipt } from '@/components/domain/Status'
import { api } from '@/services/api'
import { ref, watch } from 'vue'
import { useTranslator } from '@/locales/translator'

const props = defineProps<{ receipt: Receipt }>()

const emit = defineEmits<{
  (e: 'downloaded', result: boolean): void
}>()

const { t } = useTranslator()

const receiptHref = ref<string>()

watch(
  props.receipt,
  () => {
    api.postDownloadReceipt(props.receipt).then((data) => {
      const blob = new File([data], 'receipt.pdf', { type: 'application/pdf' })
      receiptHref.value = URL.createObjectURL(blob)
    })
  },
  { immediate: true }
)

const download = function (decision: boolean) {
  emit('downloaded', decision)
}
</script>

<template>
  <div>
    <p class="text-body-emphasis mb-2">{{ t('action.download_receipt.question') }}</p>
    <span class="btn-group">
      <a class="btn btn-primary" :href="receiptHref" download @click="download(true)">
        {{ t('action.download_receipt.download') }}
      </a>
      <button class="btn btn-secondary" @click="download(false)">
        {{ t('action.download_receipt.skip') }}
      </button>
    </span>
    <InfoPopover :message="t('action.download_receipt.send_to_auditors')" :popover="t('action.download_receipt.send_to_auditors_guards_secrecy')" />
  </div>
</template>
