<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ref, watch } from 'vue'

const emit = defineEmits<{
  (e: 'changed', result: string): void
}>()

const password = ref<string>()
watch(password, () => {
  if (!password.value) {
    return
  }

  const cleanedUp = password.value.replace(/[^0-9.]/g, '')
  if (cleanedUp.length !== 6) {
    return
  }

  emit('changed', cleanedUp)
})

const { t } = useI18n()
</script>

<template>
  <div>
    <input v-model="password" type="text" class="form-control mw-10em form-control-lg" :placeholder="t('action.set_password.set_password')" />
    <div class="form-text">
      {{ t('action.set_password.one_time_password') }}
    </div>
  </div>
</template>

<style>
.mw-10em {
  width: 10em;
}
</style>
