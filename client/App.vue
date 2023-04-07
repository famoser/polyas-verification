<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceiptPdf from '@/components/action/UploadReceiptPdf.vue'

const { t } = useI18n()

api.addInterceptors(t)
</script>

<template>
  <div class="container mb-5">
    <UploadReceiptPdf />
  </div>
</template>
