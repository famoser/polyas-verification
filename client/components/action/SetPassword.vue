<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ref, watch } from 'vue'

const emit = defineEmits<{
  (e: 'changed', result: string): void
}>()

const password = ref<number>()
watch(password, () => {
  if (!password.value || password.value < 100000) {
    return
  }

  emit('changed', String(password.value))
})

const { t } = useI18n()
</script>

<template>
  <div>
    <input v-model="password" type="number" class="form-control mw-10em form-control-lg" :placeholder="t('action.set_password.set_password')" />
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
