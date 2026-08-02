<script setup lang="ts">
import { useTranslator } from '@/locales/translator'

defineProps<{
  ownerId: string
  decision?: boolean
}>()

const emit = defineEmits<{
  (e: 'verified', result: boolean): void
}>()

const verify = function (result: boolean) {
  emit('verified', result)
}

const { t } = useTranslator()
</script>

<template>
  <p class="text-body-emphasis mb-2">{{ t('action.verify_ballot_owner.question') }}</p>
  <input type="text" class="form-control form-control-lg mb-2" disabled :value="ownerId" />
  <template v-if="decision === undefined">
    <button class="btn btn-success me-2" @click="verify(true)">
      {{ t('shared.yes') }}
    </button>
    <button class="btn btn-danger" @click="verify(false)">
      {{ t('shared.no') }}
    </button>
  </template>
  <p v-else-if="decision" class="alert alert-success mb-0">
    {{ t('action.verify_ballot_owner.successful') }}
  </p>
  <p v-else class="alert alert-danger mb-0">
    {{ t('action.verify_ballot_owner.failed') }}
  </p>
</template>

<style>
.mw-10em {
  width: 10em;
}
</style>
