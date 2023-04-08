<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import UploadReceipt from '@/components/action/UploadReceipt.vue'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'

const { t } = useI18n()

api.addInterceptors(t)
</script>

<template>
  <div class="mt-5 mb-5">
    <HeaderJumbotron />
    <div class="mt-5">
      <UploadReceipt />
    </div>
  </div>
</template>
