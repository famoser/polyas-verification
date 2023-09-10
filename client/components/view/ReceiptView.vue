<script setup lang="ts">
import type { Receipt } from '@/components/domain/Status'
import StoreReceipt from '@/components/action/StoreReceipt.vue'
import { useI18n } from 'vue-i18n'

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
      <StoreReceipt :receipt="receipt" />
    </div>
  </div>
</template>
