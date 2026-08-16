<script setup lang="ts">
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

const downloaded = function () {
  emit('downloaded', true)
}
</script>

<template>
  <div>
    <p class="text-body-emphasis mb-2">{{ t('action.download_receipt.info') }}</p>

    <a class="btn btn-secondary" :href="receiptHref" download @click="downloaded()">
      {{ t('action.download_receipt.download') }}
    </a>
  </div>
</template>
