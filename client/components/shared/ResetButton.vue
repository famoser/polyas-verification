<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useTranslator } from '@/locales/translator'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

const { t } = useTranslator()
</script>

<template>
  <button class="btn btn-secondary" @click="emit('reset')">
    <FontAwesomeIcon :icon="['fas', 'rotate']" />
    {{ t('shared.reset_button.reset') }}
  </button>
</template>

<style scoped>
.w-35em {
  width: 35em;
}
</style>
