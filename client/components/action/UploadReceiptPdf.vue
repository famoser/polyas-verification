<script setup lang="ts">
import { api } from '@/services/api'

const emit = defineEmits<{
  (e: 'uploadStarted'): void
  (e: 'uploadFinished'): void
}>()

const uploadReceipt = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.item(0)
  if (!file) {
    return
  }

  emit('uploadStarted')
  await api.postReceipt(file)
  emit('uploadFinished')
}
</script>

<template>
  <input type="file" accept="application/pdf" @change="uploadReceipt" />
</template>
