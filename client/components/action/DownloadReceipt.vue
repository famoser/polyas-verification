<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import type { Receipt } from '@/components/domain/Status'
import { api } from '@/services/api'
import { onMounted, ref, watch } from 'vue'

const props = defineProps<{ receipt: Receipt }>()

const { t } = useI18n()

const receiptHref = ref<string>()

const downloadReceipt = () => {
  console.log('receipt download')
  api.postDownloadReceipt(props.receipt).then((data) => {
    const blob = new File([data], 'receipt.pdf', { type: 'application/pdf' })
    receiptHref.value = URL.createObjectURL(blob)
  })
}

onMounted(downloadReceipt)
watch(props.receipt, downloadReceipt)
</script>

<template>
  <div>
    <a class="btn btn-primary" :href="receiptHref" download>
      {{ t('action.download_receipt.title') }}
    </a>
    <div class="form-text">
      {{ t('action.download_receipt.for_validations') }}
      <InfoPopover :message="t('action.download_receipt.possible_validations')" />
    </div>
  </div>
</template>
