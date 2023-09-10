<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useI18n } from 'vue-i18n'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

const { t } = useI18n()
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
