<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import type { Receipt, Status } from '@/components/domain/Status'
import { api } from '@/services/api'
import { computed, ref } from 'vue'

const props = defineProps<{ receipt: Receipt }>()

const receiptStatus = ref<Status>()
const storeReceipt = async () => {
  receiptStatus.value = await api.postStoreReceipt(props.receipt)
}

const { t } = useI18n()
const receiptStatusText = computed(() => {
  if (!receiptStatus.value) {
    return undefined
  }

  if (receiptStatus.value?.status) {
    return t('domain.receipt_store_status.success')
  }

  return t('domain.receipt_store_status.' + receiptStatus.value?.error)
})
</script>

<template>
  <div>
    <button v-if="!receiptStatus" class="btn btn-primary" @click="storeReceipt">
      {{ t('action.store_receipt.title') }}
    </button>
    <p v-if="receiptStatus" class="alert mb-0" :class="{ 'alert-success': receiptStatus.status, 'alert-danger': !receiptStatus.status }">
      {{ receiptStatusText }}
    </p>
    <div class="form-text">
      {{ t('action.store_receipt.privacy_is_safeguarded') }}
      <InfoPopover :message="t('action.store_receipt.privacy_is_safeguarded_help')" />
    </div>
  </div>
</template>
