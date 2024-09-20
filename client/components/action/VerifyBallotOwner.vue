<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'

defineProps<{
  ownerId: string
}>()

const emit = defineEmits<{
  (e: 'verified', result: boolean): void
}>()

const verified = ref<boolean>()
const verify = function (result: boolean) {
  verified.value = result
  emit('verified', result)
}

const { t } = useI18n()
</script>

<template>
  <p class="text-body-emphasis mb-2">{{ t('action.verify_ballot_owner.question') }}</p>
  <input type="text" class="form-control form-control-lg mb-2" disabled :value="ownerId" />
  <template v-if="verified === undefined">
    <button class="btn btn-success me-2" @click="verify(true)">
      {{ t('shared.yes') }}
    </button>
    <button class="btn btn-danger" @click="verify(false)">
      {{ t('shared.no') }}
    </button>
  </template>
  <div v-else-if="verified" class="alert alert-success">
    {{ t('action.verify_ballot_owner.successful') }}
  </div>
  <div v-else class="alert alert-danger">
    {{ t('action.verify_ballot_owner.failed') }}
  </div>
</template>

<style>
.mw-10em {
  width: 10em;
}
</style>
