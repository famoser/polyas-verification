<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import QrScanner from 'qr-scanner' // if installed via package and bundling with a module bundler like webpack or rollup

const emit = defineEmits<{
  (e: 'scanned', result: string): void
}>()

const video = ref<HTMLVideoElement>()
const qrScanner = ref<QrScanner>()

onMounted(() => {
  if (video.value) {
    qrScanner.value = new QrScanner(video.value, (result) => emit('scanned', result.data), { returnDetailedScanResult: true })
    qrScanner.value.start()
  }
})

onUnmounted(() => {
  qrScanner.value?.destroy()
})
</script>

<template>
  <video ref="video" class="w-100"></video>
</template>
