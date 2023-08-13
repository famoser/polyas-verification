<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed, onMounted, ref, watch } from 'vue'

const emit = defineEmits<{
  (e: 'changed', result: string): void
}>()

const passwordInput = ref<HTMLElement>()
const password = ref<string>()
const sanitizedPassword = computed(() => password.value?.replace(/[^0-9.]/g, ''))
watch(password, () => {
  if (!sanitizedPassword.value || sanitizedPassword.value.length !== 6) {
    return
  }

  emit('changed', sanitizedPassword.value)
})

const invalidCharacters = computed(() => password.value && sanitizedPassword.value?.length !== password.value.length)

onMounted(() => {
  passwordInput.value?.focus()
})

const { t } = useI18n()
</script>

<template>
  <div class="d-flex flex-column align-items-center">
    <input
      ref="passwordInput"
      type="text"
      class="form-control mw-10em form-control-lg text-center"
      :placeholder="t('action.set_password.set_password')"
      v-model="password"
      :class="{ 'is-invalid': invalidCharacters }"
    />
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
