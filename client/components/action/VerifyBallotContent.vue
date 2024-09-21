<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BallotsView from '@/components/view/BallotsView.vue'

defineProps<{
  choice: string
  decision?: boolean
}>()

const emit = defineEmits<{
  (e: 'verified', result: boolean): void
}>()

const verify = function (result: boolean) {
  emit('verified', result)
}

const { t } = useI18n()
</script>

<template>
  <p class="text-body-emphasis mb-2">{{ t('action.verify_ballot_content.question') }}</p>

  <BallotsView class="mb-2" :choice="choice" />

  <template v-if="decision === undefined">
    <button class="btn btn-success me-2" @click="verify(true)">
      {{ t('shared.yes') }}
    </button>
    <button class="btn btn-danger" @click="verify(false)">
      {{ t('shared.no') }}
    </button>
  </template>
  <p v-else-if="decision" class="alert alert-success mb-0">
    {{ t('action.verify_ballot_content.successful') }}
  </p>
  <p v-else class="alert alert-danger mb-0">
    {{ t('action.verify_ballot_content.failed') }}
  </p>
</template>

<style>
.mw-10em {
  width: 10em;
}
</style>
