<script setup lang="ts">
import type { Receipt } from '@/components/domain/Status'
import StoreReceipt from '@/components/action/StoreReceipt.vue'
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import DownloadReceipt from '@/components/action/DownloadReceipt.vue'

defineProps<{
  receipt: Receipt
}>()

const { t } = useI18n()
</script>

<template>
  <div class="card">
    <div class="card-header">
      <p class="mb-0">
        <b>
          {{ t(`view.receipt_view.receipt`) }}
        </b>
      </p>
    </div>
    <div class="card-body">
      <div class="form-text mb-4">
        {{ t('view.receipt_view.privacy_is_safeguarded') }}
        <InfoPopover :message="t('view.receipt_view.privacy_is_safeguarded_help')" />
      </div>
      <StoreReceipt :receipt="receipt" class="mb-4" />
      <DownloadReceipt :receipt="receipt" />
    </div>
  </div>
</template>
