<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import type { Receipt } from '@/components/domain/Status'
import { api } from '@/services/api'
import { ref, watch } from 'vue'

const props = defineProps<{ receipt: Receipt }>()

const { t } = useI18n()

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
</script>

<template>
  <div>
    <a class="btn btn-primary" :href="receiptHref" download>
      {{ t('action.download_receipt.title') }}
    </a>
    <InfoPopover :message="t('action.download_receipt.for_validations')" :popover="t('action.download_receipt.possible_validations')" />
  </div>
</template>
