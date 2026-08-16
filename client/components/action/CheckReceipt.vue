<script setup lang="ts">
import InfoPopover from '@/components/shared/InfoPopover.vue'
import type { Receipt } from '@/components/domain/Status'
import { api } from '@/services/api'
import { useTranslator } from '@/locales/translator'

const props = defineProps<{ receipt: Receipt }>()

const emit = defineEmits<{
  (e: 'checked', result: boolean): void
}>()

const { t } = useTranslator()

const cont = function () {
  api.postReceipt(props.receipt).then(() => {
    emit('checked', true)
  })
}
</script>

<template>
  <div>
    <p class="text-body-emphasis mb-2">{{ t('action.check_receipt.info') }}</p>
    <InfoPopover class="mb-2" :message="t('action.check_receipt.store_receipt')" :popover="t('action.check_receipt.receipt_popover')" />

    <button class="btn btn-primary" @click="cont">
      {{ t('action.check_receipt.continue') }}
    </button>
  </div>
</template>
